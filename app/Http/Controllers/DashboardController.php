<?php

namespace App\Http\Controllers;

use App\Enums\ConnectivityStatus;
use App\Enums\PowerState;
use App\Models\AcUnit;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Services\UsageAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private UsageAggregationService $usage) {}

    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $tz = $organization->timezone ?: 'UTC';
        $today = now($tz)->startOfDay();

        $range = (int) $request->integer('range', 7);
        if (! in_array($range, [1, 7, 30], true)) {
            $range = 7;
        }

        $siteId = $request->integer('site_id') ?: null;
        $filters = ['site_id' => $siteId];

        $this->usage->ensureToday($organization);

        $acQuery = AcUnit::where('organization_id', $organization->id)
            ->with([
                'stateSnapshot',
                'currentAssignment.device',
                'space.floor.building.site',
                'acModel',
            ]);

        if ($siteId) {
            $acQuery->whereHas('space.floor.building', fn ($query) => $query->where('site_id', $siteId));
        }

        $acs = $acQuery->get();

        $on = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOn && ! $ac->stateSnapshot->isStale())->count();
        $off = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOff && ! $ac->stateSnapshot->isStale())->count();
        $stale = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->isStale() && $ac->stateSnapshot?->power_state !== PowerState::Unknown)->count();
        $unknown = max(0, $acs->count() - $on - $off - $stale);

        $devices = $organization->devices()
            ->with('currentAssignment.acUnit.space.floor.building.site')
            ->when($siteId, function ($query) use ($siteId) {
                $query->whereHas('currentAssignment.acUnit.space.floor.building', fn ($q) => $q->where('site_id', $siteId));
            })
            ->get();

        $onlineDevices = $devices->filter(fn ($device) => $device->connectivity() === ConnectivityStatus::Online)->count();
        $offlineDevices = $devices->filter(fn ($device) => $device->connectivity() === ConnectivityStatus::Offline)->count();
        $unknownDevices = $devices->filter(fn ($device) => $device->connectivity() === ConnectivityStatus::Unknown)->count();

        $from = $today->copy()->subDays($range - 1);
        $series = $this->usage->dailySeries($organization, $from, $today, $filters);
        $todayTotals = $this->usage->totals($organization, $today, $today, $filters);
        $rangeTotals = $this->usage->totals($organization, $from, $today, $filters);

        $todayByAc = DB::table('ac_daily_usage')
            ->where('organization_id', $organization->id)
            ->where('usage_date', $today->toDateString())
            ->pluck('on_seconds', 'ac_unit_id');

        $byBuilding = $acs->groupBy(fn (AcUnit $ac) => $ac->space?->floor?->building?->name ?? 'Unassigned')
            ->map(function ($group, $building) use ($todayByAc) {
                $on = $group->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOn && ! $ac->stateSnapshot->isStale())->count();
                $off = $group->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOff && ! $ac->stateSnapshot->isStale())->count();

                return [
                    'name' => $building,
                    'total' => $group->count(),
                    'on' => $on,
                    'off' => $off,
                    'unknown' => max(0, $group->count() - $on - $off),
                    'today_seconds' => $group->sum(fn (AcUnit $ac) => (int) ($todayByAc[$ac->id] ?? 0)),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $alerts = Notification::where('organization_id', $organization->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->whereIn('severity', ['alert', 'warning'])
            ->with('acUnit')
            ->orderByDesc('occurred_at')
            ->limit(5)
            ->get();

        $recentActivity = ActivityLog::where('organization_id', $organization->id)
            ->with(['acUnit', 'user', 'device'])
            ->orderByDesc('occurred_at')
            ->limit(6)
            ->get();

        $sites = $organization->sites()->orderBy('name')->get();

        return view('dashboard', [
            'organization' => $organization,
            'acs' => $acs,
            'sites' => $sites,
            'siteId' => $siteId,
            'range' => $range,
            'from' => $from,
            'today' => $today,
            'stats' => [
                'total' => $acs->count(),
                'on' => $on,
                'off' => $off,
                'unknown' => $unknown,
                'stale' => $stale,
                'online_devices' => $onlineDevices,
                'offline_devices' => $offlineDevices,
                'unknown_devices' => $unknownDevices,
            ],
            'todayOnSeconds' => $todayTotals['on_seconds'],
            'rangeOnSeconds' => $rangeTotals['on_seconds'],
            'series' => $series,
            'byBuilding' => $byBuilding,
            'alerts' => $alerts,
            'recentActivity' => $recentActivity,
            'chart' => [
                'state' => [
                    ['label' => 'Known ON', 'value' => $on, 'color' => '#20B96B'],
                    ['label' => 'Known OFF', 'value' => $off, 'color' => '#8798A9'],
                    ['label' => 'Unknown', 'value' => $unknown, 'color' => '#C7D4E1'],
                    ['label' => 'Stale', 'value' => $stale, 'color' => '#F2A52B'],
                ],
                'trend' => $series,
            ],
        ]);
    }
}
