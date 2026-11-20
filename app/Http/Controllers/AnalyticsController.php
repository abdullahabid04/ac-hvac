<?php

namespace App\Http\Controllers;

use App\Models\AcUnit;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Site;
use App\Models\Space;
use App\Services\UsageAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function __construct(private UsageAggregationService $usage) {}

    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $tz = $organization->timezone ?: 'UTC';

        $period = (string) $request->query('period', '7d');
        if (! in_array($period, ['today', 'yesterday', '7d', '30d', 'custom'], true)) {
            $period = '7d';
        }

        $today = now($tz)->startOfDay();
        [$from, $to] = $this->resolvePeriod($period, $request, $tz, $today);

        $filters = [
            'site_id' => $request->integer('site_id') ?: null,
            'building_id' => $request->integer('building_id') ?: null,
            'floor_id' => $request->integer('floor_id') ?: null,
            'space_id' => $request->integer('space_id') ?: null,
            'ac_id' => $request->integer('ac_id') ?: null,
            'q' => trim((string) $request->query('q', '')),
        ];

        $tab = (string) $request->query('tab', 'overview');
        if (! in_array($tab, ['overview', 'setpoint', 'location', 'ac'], true)) {
            $tab = 'overview';
        }

        $this->usage->ensureToday($organization);

        $totals = $this->usage->totals($organization, $from, $to, $filters);
        $daily = $this->usage->dailySeries($organization, $from, $to, $filters);
        $byBuilding = $this->usage->byBuilding($organization, $from, $to, $filters);
        $byFloor = $this->usage->byFloor($organization, $from, $to, $filters);
        $bySpace = $this->usage->bySpace($organization, $from, $to, $filters);
        $byAc = $this->usage->byAc($organization, $from, $to, $filters);
        $averageSetpoint = $this->usage->averageSetpoint($organization, $from, $to, $filters);
        $setpointSeries = $this->usage->setpointSeries($organization, $from, $to, $filters);
        $mostUsed = $bySpace->first();

        $acCount = max(1, $totals['ac_count']);
        $averagePerAc = (int) round($totals['on_seconds'] / $acCount);

        $scopedAcCount = AcUnit::where('organization_id', $organization->id)
            ->when($filters['site_id'], fn ($q, $id) => $q->whereHas('space.floor.building', fn ($x) => $x->where('site_id', $id)))
            ->when($filters['building_id'], fn ($q, $id) => $q->whereHas('space.floor', fn ($x) => $x->where('building_id', $id)))
            ->when($filters['floor_id'], fn ($q, $id) => $q->whereHas('space', fn ($x) => $x->where('floor_id', $id)))
            ->when($filters['space_id'], fn ($q, $id) => $q->where('space_id', $id))
            ->when($filters['ac_id'], fn ($q, $id) => $q->whereKey($id))
            ->count();

        return view('analytics', [
            'organization' => $organization,
            'timezone' => $tz,
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'filters' => $filters,
            'tab' => $tab,
            'totals' => $totals,
            'averagePerAc' => $averagePerAc,
            'daily' => $daily,
            'byBuilding' => $byBuilding,
            'byFloor' => $byFloor,
            'bySpace' => $bySpace,
            'byAc' => $byAc,
            'averageSetpoint' => $averageSetpoint,
            'setpointSeries' => $setpointSeries,
            'mostUsed' => $mostUsed,
            'scopedAcCount' => $scopedAcCount,
            'sites' => Site::where('organization_id', $organization->id)->orderBy('name')->get(),
            'buildings' => Building::where('organization_id', $organization->id)->orderBy('name')->get(),
            'floors' => Floor::where('organization_id', $organization->id)->orderBy('name')->get(),
            'spaces' => Space::where('organization_id', $organization->id)->with('floor.building')->orderBy('name')->get(),
            'acs' => AcUnit::where('organization_id', $organization->id)->orderBy('name')->get(),
            'chart' => [
                'daily' => $daily,
                'setpoints' => $setpointSeries,
                'buildings' => $byBuilding->map(fn ($row) => [
                    'label' => $row->building_name,
                    'value' => (int) $row->on_seconds,
                ])->values()->all(),
            ],
        ]);
    }

    private function resolvePeriod(string $period, Request $request, string $tz, Carbon $today): array
    {
        return match ($period) {
            'today' => [$today->copy(), $today->copy()],
            'yesterday' => [$today->copy()->subDay(), $today->copy()->subDay()],
            '30d' => [$today->copy()->subDays(29), $today->copy()],
            'custom' => [
                Carbon::parse($request->query('from', $today->copy()->subDays(6)->toDateString()), $tz)->startOfDay(),
                Carbon::parse($request->query('to', $today->toDateString()), $tz)->startOfDay(),
            ],
            default => [$today->copy()->subDays(6), $today->copy()],
        };
    }
}
