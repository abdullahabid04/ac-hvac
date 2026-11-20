<?php

namespace App\Http\Controllers;

use App\Enums\EventSource;
use App\Models\AcUnit;
use App\Models\ActivityLog;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Site;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $tz = $organization->timezone ?: 'UTC';

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'source' => (string) $request->query('source', ''),
            'result' => (string) $request->query('result', ''),
            'ac_id' => $request->integer('ac_id') ?: null,
            'site_id' => $request->integer('site_id') ?: null,
            'from' => (string) $request->query('from', ''),
            'to' => (string) $request->query('to', ''),
        ];

        $query = ActivityLog::where('organization_id', $organization->id)
            ->with(['acUnit.space.floor.building.site', 'user', 'device'])
            ->orderByDesc('occurred_at');

        if ($filters['q'] !== '') {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                    ->orWhere('action', 'like', $term)
                    ->orWhereHas('acUnit', fn ($ac) => $ac->where('name', 'like', $term)->orWhere('asset_code', 'like', $term));
            });
        }

        if ($filters['source'] !== '' && EventSource::tryFrom($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if ($filters['result'] !== '') {
            $query->where('result', $filters['result']);
        }

        if ($filters['ac_id']) {
            $query->where('ac_unit_id', $filters['ac_id']);
        }

        if ($filters['site_id']) {
            $query->whereHas('acUnit.space.floor.building', fn ($q) => $q->where('site_id', $filters['site_id']));
        }

        if ($filters['from'] !== '') {
            $query->where('occurred_at', '>=', Carbon::parse($filters['from'], $tz)->startOfDay()->utc());
        }

        if ($filters['to'] !== '') {
            $query->where('occurred_at', '<=', Carbon::parse($filters['to'], $tz)->endOfDay()->utc());
        }

        $activity = $query->paginate(25)->withQueryString();

        return view('activity', [
            'organization' => $organization,
            'timezone' => $tz,
            'activity' => $activity,
            'filters' => $filters,
            'sources' => EventSource::cases(),
            'results' => ['success', 'pending', 'warning', 'failure', 'info'],
            'acs' => AcUnit::where('organization_id', $organization->id)->orderBy('name')->get(),
            'sites' => Site::where('organization_id', $organization->id)->orderBy('name')->get(),
        ]);
    }
}
