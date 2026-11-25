<?php

namespace App\Http\Controllers;

use App\Enums\CommandStatus;
use App\Enums\ConnectivityStatus;
use App\Enums\EventSource;
use App\Enums\PolicyScope;
use App\Enums\PolicyValue;
use App\Enums\PowerState;
use App\Enums\StateQuality;
use App\Models\AcCommand;
use App\Models\AcModel;
use App\Models\AcStateSnapshot;
use App\Models\AcUnit;
use App\Models\Building;
use App\Models\Device;
use App\Models\Floor;
use App\Models\IrProfileVersion;
use App\Models\RemotePolicy;
use App\Models\Site;
use App\Models\Space;
use App\Services\AcCommandService;
use App\Services\ActivityService;
use App\Services\AuditService;
use App\Services\DeviceAssignmentService;
use App\Services\RemotePolicyResolver;
use App\Services\UsageAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AcController extends Controller
{
    public function __construct(
        private AcCommandService $commands,
        private UsageAggregationService $usage,
        private RemotePolicyResolver $policies,
        private DeviceAssignmentService $assignments,
        private ActivityService $activity,
        private AuditService $audit,
    ) {}

    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $today = now($organization->timezone ?: 'UTC')->startOfDay();

        $this->usage->ensureToday($organization);

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'site_id' => $request->integer('site_id') ?: null,
            'building_id' => $request->integer('building_id') ?: null,
            'floor_id' => $request->integer('floor_id') ?: null,
            'space_id' => $request->integer('space_id') ?: null,
            'state' => (string) $request->query('state', ''),
            'connectivity' => (string) $request->query('connectivity', ''),
            'sort' => (string) $request->query('sort', 'asset_code'),
            'direction' => $request->query('direction') === 'desc' ? 'desc' : 'asc',
            'view' => $request->query('view') === 'table' ? 'table' : 'grid',
        ];

        $query = AcUnit::where('organization_id', $organization->id)
            ->with([
                'stateSnapshot',
                'currentAssignment.device',
                'space.floor.building.site',
                'acModel',
                'irProfileVersion',
            ]);

        $this->applyAcFilters($query, $filters, $organization->id);

        $sort = $filters['sort'];
        $direction = $filters['direction'];
        $usageSub = DB::table('ac_daily_usage')
            ->selectRaw('ac_unit_id, SUM(on_seconds) as on_seconds')
            ->where('organization_id', $organization->id)
            ->where('usage_date', $today->toDateString())
            ->groupBy('ac_unit_id');

        if ($sort === 'duration') {
            $query->leftJoinSub($usageSub, 'today_usage', 'today_usage.ac_unit_id', '=', 'ac_units.id')
                ->select('ac_units.*')
                ->orderBy('today_usage.on_seconds', $direction);
        } elseif (in_array($sort, ['setpoint', 'last_update'], true)) {
            $query->leftJoin('ac_state_snapshots as sort_snapshot', 'sort_snapshot.ac_unit_id', '=', 'ac_units.id')
                ->select('ac_units.*')
                ->orderBy($sort === 'setpoint' ? 'sort_snapshot.temperature_setpoint_c' : 'sort_snapshot.observed_at', $direction);
        } else {
            $query->orderBy(in_array($sort, ['name', 'asset_code'], true) ? "ac_units.{$sort}" : 'ac_units.asset_code', $direction);
        }

        $perPage = $filters['view'] === 'table' ? 20 : 12;
        $acs = $query->paginate($perPage)->withQueryString();

        $todayUsage = DB::table('ac_daily_usage')
            ->where('organization_id', $organization->id)
            ->where('usage_date', $today->toDateString())
            ->whereIn('ac_unit_id', $acs->pluck('id'))
            ->pluck('on_seconds', 'ac_unit_id');

        $viewData = [
            'organization' => $organization,
            'acs' => $acs,
            'todayUsage' => $todayUsage,
            'today' => $today,
            'filters' => $filters,
            'sites' => $organization->sites()->orderBy('name')->get(),
            'buildings' => Building::where('organization_id', $organization->id)->orderBy('name')->get(),
            'floors' => Floor::where('organization_id', $organization->id)->orderBy('name')->get(),
            'spaces' => Space::where('organization_id', $organization->id)->with('floor.building')->orderBy('name')->get(),
            'acModels' => AcModel::with(['profiles.versions'])->orderBy('manufacturer')->get(),
            'profileVersions' => IrProfileVersion::with('profile.acModel')->orderBy('id')->get(),
        ];

        if ($request->ajax() || $request->boolean('partial')) {
            return view('acs.partials.results', $viewData);
        }

        return view('acs.index', $viewData);
    }

    public function store(\App\Http\Requests\StoreAcUnitRequest $request)
    {
        $organization = $request->attributes->get('organization');
        $ac = AcUnit::create(array_merge($request->validated(), [
            'organization_id' => $organization->id,
        ]));

        AcStateSnapshot::firstOrCreate(
            ['ac_unit_id' => $ac->id],
            [
                'organization_id' => $organization->id,
                'power_state' => PowerState::Unknown,
                'quality' => StateQuality::Unknown,
            ]
        );

        $this->audit->record($organization, $request->user(), 'ac.created', 'ac_unit', $ac->id, null, $ac->toArray(), $request);
        $this->activity->record($organization, $ac, EventSource::Portal, 'ac_registered', "Registered air conditioner {$ac->asset_code} — {$ac->name}.", 'success', $request->user());

        return redirect()->route('acs.show', $ac)->with('success', 'Air conditioner registered.');
    }

    public function show(Request $request, AcUnit $ac)
    {
        $this->authorize('view', $ac);
        $organization = $request->attributes->get('organization');
        $tz = $organization->timezone ?: 'UTC';
        $tab = in_array($request->query('tab'), ['overview', 'history', 'usage', 'settings'], true)
            ? $request->query('tab')
            : 'overview';

        $ac->load([
            'space.floor.building.site',
            'acModel',
            'irProfileVersion.profile.acModel',
            'stateSnapshot',
            'currentAssignment.device',
            'assignments.device',
            'commands' => fn ($q) => $q->with('requestedBy')->orderByDesc('issued_at')->limit(8),
            'deviceEvents' => fn ($q) => $q->orderByDesc('device_occurred_at')->limit(15),
            'activityLogs' => fn ($q) => $q->with(['user', 'device'])->orderByDesc('occurred_at')->limit(20),
            'stateHistory' => fn ($q) => $q->orderByDesc('observed_at')->limit(20),
        ]);

        $this->usage->ensureToday($organization);

        $today = now($tz)->startOfDay();
        $todayTotals = $this->usage->totals($organization, $today, $today, ['ac_id' => $ac->id]);
        $weekTotals = $this->usage->totals($organization, $today->copy()->subDays(6), $today, ['ac_id' => $ac->id]);
        $monthTotals = $this->usage->totals($organization, $today->copy()->subDays(29), $today, ['ac_id' => $ac->id]);
        $daily = $this->usage->dailySeries($organization, $today->copy()->subDays(13), $today, ['ac_id' => $ac->id]);

        $effectivePolicy = $this->policies->effective($ac);
        $explicitPolicy = RemotePolicy::where('organization_id', $organization->id)
            ->where('scope_type', PolicyScope::Ac->value)
            ->where('scope_id', $ac->id)
            ->first();

        $availableDevices = Device::where('organization_id', $organization->id)
            ->where('status', 'active')
            ->with('currentAssignment.acUnit')
            ->orderBy('name')
            ->get();

        return view('acs.show', [
            'organization' => $organization,
            'ac' => $ac,
            'tab' => $tab,
            'timezone' => $tz,
            'today' => $today,
            'usage' => [
                'today' => $todayTotals,
                'week' => $weekTotals,
                'month' => $monthTotals,
                'daily' => $daily,
            ],
            'effectivePolicy' => $effectivePolicy,
            'explicitPolicy' => $explicitPolicy,
            'availableDevices' => $availableDevices,
            'acModels' => AcModel::with(['profiles.versions'])->orderBy('manufacturer')->get(),
            'profileVersions' => IrProfileVersion::with('profile.acModel')->orderBy('id')->get(),
            'spaces' => Space::where('organization_id', $organization->id)->with('floor.building')->orderBy('name')->get(),
        ]);
    }

    public function control(Request $request, AcUnit $ac)
    {
        $this->authorize('control', $ac);
        $organization = $request->attributes->get('organization');
        $tz = $organization->timezone ?: 'UTC';

        $ac->load([
            'space.floor.building.site',
            'acModel',
            'irProfileVersion',
            'stateSnapshot',
            'currentAssignment.device',
            'commands' => fn ($q) => $q->orderByDesc('issued_at')->limit(6),
        ]);

        $latestCommand = $ac->commands->first();
        $effectivePolicy = $this->policies->effective($ac);
        $profile = $ac->irProfileVersion;

        return view('acs.control', [
            'organization' => $organization,
            'ac' => $ac,
            'timezone' => $tz,
            'latestCommand' => $latestCommand,
            'effectivePolicy' => $effectivePolicy,
            'temperatureMin' => $profile?->temperature_min_c,
            'temperatureMax' => $profile?->temperature_max_c,
            'temperatureStep' => $profile?->temperature_step_c ?? 1,
            'capabilities' => $profile?->capabilities ?? [],
            'canControl' => $ac->supportsProfileControl() && $ac->currentAssignment?->device,
            'controlConfig' => [
                'acId' => $ac->id,
                'commandUrl' => route('acs.commands.store', $ac),
                'commandPollUrl' => route('commands.show', ['command' => '__COMMAND__']),
                'power' => $ac->stateSnapshot?->power_state?->value ?? 'unknown',
                'setpoint' => $ac->stateSnapshot?->temperature_setpoint_c,
                'desired_power' => $ac->desired_power_state,
                'desired_setpoint' => $ac->desired_temperature_setpoint_c,
                'min' => $profile?->temperature_min_c,
                'max' => $profile?->temperature_max_c,
                'step' => (float) ($profile?->temperature_step_c ?? 1),
                'canControl' => (bool) ($ac->supportsProfileControl() && $ac->currentAssignment?->device),
                'deviceOnline' => $ac->connectivity() === ConnectivityStatus::Online,
            ],
        ]);
    }

    public function command(\App\Http\Requests\StoreAcCommandRequest $request, AcUnit $ac)
    {
        $this->authorize('control', $ac);
        $organization = $request->attributes->get('organization');
        $command = $this->commands->create($organization, $ac, $request->validated(), $request->user());

        // Persist desired state directly on ac_units so the heartbeat API
        // can serve it without depending on command acknowledgement.
        $desiredState = $command->desired_state ?? [];
        $updateData = [];
        if (isset($desiredState['power'])) {
            $updateData['desired_power_state'] = strtolower((string) $desiredState['power']);
        }
        if (isset($desiredState['temperature_setpoint_c']) && is_numeric($desiredState['temperature_setpoint_c'])) {
            $updateData['desired_temperature_setpoint_c'] = (int) $desiredState['temperature_setpoint_c'];
        }
        if (! empty($updateData)) {
            $ac->update($updateData);
        }

        return response()->json([
            'data' => $this->commandPayload($command),
            'meta' => [
                'request_id' => $request->attributes->get('request_id'),
                'delivery_note' => $ac->connectivity() === ConnectivityStatus::Online
                    ? 'Command queued for the assigned device.'
                    : 'The device is offline or has not reported recently. The command is queued until its expiry; it will not execute after expiry.',
            ],
        ], 201);
    }

    public function commandStatus(Request $request, AcCommand $command)
    {
        $organization = $request->attributes->get('organization');

        if ($command->organization_id !== $organization->id) {
            abort(404);
        }

        return response()->json([
            'data' => $this->commandPayload($command->load(['acUnit.stateSnapshot', 'acUnit.currentAssignment.device'])),
            'meta' => ['request_id' => $request->attributes->get('request_id')],
        ]);
    }

    public function update(\App\Http\Requests\UpdateAcUnitRequest $request, AcUnit $ac)
    {
        $this->authorize('update', $ac);
        $organization = $request->attributes->get('organization');
        $before = $ac->toArray();
        $ac->update($request->validated());

        $this->audit->record($organization, $request->user(), 'ac.updated', 'ac_unit', $ac->id, $before, $ac->fresh()->toArray(), $request);
        $this->activity->record($organization, $ac, EventSource::Portal, 'ac_updated', "Updated air conditioner {$ac->asset_code}.", 'success', $request->user());

        return back()->with('success', 'Air conditioner updated.');
    }

    public function assignDevice(\App\Http\Requests\AssignDeviceRequest $request, AcUnit $ac)
    {
        $this->authorize('update', $ac);
        $organization = $request->attributes->get('organization');
        $device = $request->filled('device_id') ? Device::where('organization_id', $organization->id)->find($request->integer('device_id')) : null;

        $this->assignments->assign($organization, $ac, $device, $request->user());

        return back()->with('success', $device ? 'Device assigned.' : 'Device unassigned.');
    }

    public function updateRemotePolicy(\App\Http\Requests\UpdateRemotePolicyRequest $request, AcUnit $ac)
    {
        $this->authorize('update', $ac);
        $organization = $request->attributes->get('organization');

        $this->policies->setPolicy(
            $organization,
            PolicyScope::Ac,
            $ac->id,
            PolicyValue::from($request->input('policy_value')),
            $request->user(),
        );

        return back()->with('success', 'AC remote policy updated.');
    }

    private function applyAcFilters($query, array $filters, int $organizationId): void
    {
        if ($filters['site_id']) {
            $query->whereHas('space.floor.building', fn ($q) => $q->where('site_id', $filters['site_id']));
        }
        if ($filters['building_id']) {
            $query->whereHas('space.floor', fn ($q) => $q->where('building_id', $filters['building_id']));
        }
        if ($filters['floor_id']) {
            $query->whereHas('space', fn ($q) => $q->where('floor_id', $filters['floor_id']));
        }
        if ($filters['space_id']) {
            $query->where('space_id', $filters['space_id']);
        }
        if ($filters['q'] !== '') {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('ac_units.name', 'like', $term)
                    ->orWhere('ac_units.asset_code', 'like', $term)
                    ->orWhereHas('acModel', fn ($m) => $m->where('manufacturer', 'like', $term)->orWhere('model_name', 'like', $term))
                    ->orWhereHas('space', fn ($s) => $s->where('name', 'like', $term));
            });
        }

        $staleThreshold = now()->subMinutes((int) config('hvac.state_stale_after_minutes', 120));

        if ($filters['state'] === 'on') {
            $query->whereHas('stateSnapshot', fn ($q) => $q->where('power_state', PowerState::KnownOn->value)->where('observed_at', '>=', $staleThreshold));
        } elseif ($filters['state'] === 'off') {
            $query->whereHas('stateSnapshot', fn ($q) => $q->where('power_state', PowerState::KnownOff->value)->where('observed_at', '>=', $staleThreshold));
        } elseif ($filters['state'] === 'unknown') {
            $query->where(function ($q) {
                $q->whereDoesntHave('stateSnapshot')
                    ->orWhereHas('stateSnapshot', fn ($s) => $s->where('power_state', PowerState::Unknown->value));
            });
        } elseif ($filters['state'] === 'stale') {
            $query->whereHas('stateSnapshot', fn ($q) => $q->where('observed_at', '<', $staleThreshold)->where('power_state', '!=', PowerState::Unknown->value));
        }

        $offlineThreshold = now()->subMinutes((int) config('hvac.device_offline_after_minutes', 5));

        if ($filters['connectivity'] === 'online') {
            $query->whereHas('currentAssignment.device', fn ($q) => $q->where('status', 'active')->where('last_seen_at', '>=', $offlineThreshold));
        } elseif ($filters['connectivity'] === 'offline') {
            $query->whereHas('currentAssignment.device', fn ($q) => $q->where(function ($d) use ($offlineThreshold) {
                $d->whereIn('status', ['revoked', 'suspended', 'unprovisioned'])
                    ->orWhere(fn ($x) => $x->whereNotNull('last_seen_at')->where('last_seen_at', '<', $offlineThreshold));
            }));
        } elseif ($filters['connectivity'] === 'unknown') {
            $query->where(function ($q) {
                $q->whereDoesntHave('currentAssignment.device')
                    ->orWhereHas('currentAssignment.device', fn ($d) => $d->where('status', 'active')->whereNull('last_seen_at'));
            });
        }
    }

    public function commandPayload(AcCommand $command): array
    {
        $command->loadMissing(['acUnit.stateSnapshot', 'acUnit.currentAssignment.device', 'device']);

        return [
            'id' => $command->id,
            'status' => $command->status->value,
            'status_label' => $command->status->label(),
            'is_pending' => $command->status->isPending(),
            'is_terminal' => $command->status->isTerminal(),
            'desired_state' => $command->desired_state,
            'issued_at' => $command->issued_at?->toIso8601String(),
            'expires_at' => $command->expires_at?->toIso8601String(),
            'published_at' => $command->published_at?->toIso8601String(),
            'received_at' => $command->received_at?->toIso8601String(),
            'transmission_at' => $command->transmission_at?->toIso8601String(),
            'completed_at' => $command->completed_at?->toIso8601String(),
            'failure_code' => $command->failure_code,
            'failure_message' => $command->failure_message,
            'ac' => [
                'id' => $command->acUnit?->id,
                'name' => $command->acUnit?->name,
                'asset_code' => $command->acUnit?->asset_code,
            ],
            'device' => [
                'name' => $command->device?->name,
                'connectivity' => $command->device?->connectivity()->value,
            ],
            'last_reported' => $command->acUnit?->stateSnapshot ? [
                'power_state' => $command->acUnit->stateSnapshot->power_state->value,
                'power_state_label' => $command->acUnit->stateSnapshot->power_state->label(),
                'temperature_setpoint_c' => $command->acUnit->stateSnapshot->temperature_setpoint_c,
                'observed_at' => $command->acUnit->stateSnapshot->observed_at?->toIso8601String(),
                'quality' => $command->acUnit->stateSnapshot->quality->value,
            ] : null,
        ];
    }
}
