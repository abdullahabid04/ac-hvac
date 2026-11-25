<?php

namespace App\Http\Controllers\Api;

use App\Enums\CommandStatus;
use App\Enums\ConnectivityStatus;
use App\Enums\EventSource;
use App\Enums\PolicyScope;
use App\Enums\PolicyValue;
use App\Enums\PowerState;
use App\Http\Requests\GenerateReportRequest;
use App\Http\Requests\StoreAcCommandRequest;
use App\Http\Requests\UpdateRemotePolicyRequest;
use App\Models\AcCommand;
use App\Models\AcUnit;
use App\Models\ActivityLog;
use App\Models\Building;
use App\Models\Device;
use App\Models\Floor;
use App\Models\GeneratedReport;
use App\Models\Notification;
use App\Models\Site;
use App\Models\Space;
use App\Services\AcCommandService;
use App\Services\RemotePolicyResolver;
use App\Services\ReportService;
use App\Services\UsageAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ManagerController extends ApiController
{
    public function __construct(
        private AcCommandService $commands,
        private UsageAggregationService $usage,
        private RemotePolicyResolver $policies,
        private ReportService $reports,
    ) {}

    public function me(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $user = $request->user();

        return $this->ok([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'job_title' => $user->job_title,
            ],
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
                'timezone' => $organization->timezone,
            ],
            'role' => 'organization_admin',
            'unread_notifications' => Notification::where('organization_id', $organization->id)
                ->where('user_id', $user->id)->whereNull('read_at')->count(),
        ]);
    }

    public function overview(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $tz = $organization->timezone ?: 'UTC';
        $today = now($tz)->startOfDay();
        $days = min(30, max(1, (int) $request->integer('days', 7)));
        $from = $today->copy()->subDays($days - 1);

        $this->usage->ensureToday($organization);

        $acs = AcUnit::where('organization_id', $organization->id)
            ->with(['stateSnapshot', 'currentAssignment.device'])
            ->get();

        $on = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOn && ! $ac->stateSnapshot->isStale())->count();
        $off = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOff && ! $ac->stateSnapshot->isStale())->count();
        $stale = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->isStale() && $ac->stateSnapshot?->power_state !== PowerState::Unknown)->count();

        $devices = $organization->devices()->get();
        $totals = $this->usage->totals($organization, $today, $today);

        return $this->ok([
            'acs' => [
                'total' => $acs->count(),
                'known_on' => $on,
                'known_off' => $off,
                'unknown' => max(0, $acs->count() - $on - $off - $stale),
                'stale' => $stale,
            ],
            'devices' => [
                'total' => $devices->count(),
                'online' => $devices->filter(fn (Device $d) => $d->connectivity() === ConnectivityStatus::Online)->count(),
                'offline' => $devices->filter(fn (Device $d) => $d->connectivity() === ConnectivityStatus::Offline)->count(),
                'unknown' => $devices->filter(fn (Device $d) => $d->connectivity() === ConnectivityStatus::Unknown)->count(),
            ],
            'today_on_seconds' => $totals['on_seconds'],
            'trend' => $this->usage->dailySeries($organization, $from, $today),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function locations(Request $request)
    {
        $organization = $request->attributes->get('organization');

        $sites = Site::where('organization_id', $organization->id)
            ->with(['buildings.floors.spaces.childrenRecursive'])
            ->orderBy('name')
            ->get();

        return $this->ok($sites->map(fn (Site $site) => [
            'id' => $site->id,
            'type' => 'site',
            'name' => $site->name,
            'code' => $site->code,
            'address' => $site->address,
            'buildings' => $site->buildings->map(fn (Building $building) => [
                'id' => $building->id,
                'type' => 'building',
                'name' => $building->name,
                'floors' => $building->floors->map(fn (Floor $floor) => [
                    'id' => $floor->id,
                    'type' => 'floor',
                    'name' => $floor->name,
                    'floor_number' => $floor->floor_number,
                    'spaces' => $floor->spaces->map(fn (Space $space) => [
                        'id' => $space->id,
                        'type' => 'space',
                        'name' => $space->name,
                        'space_type' => $space->space_type,
                        'children' => $space->childrenRecursive->map(fn (Space $child) => [
                            'id' => $child->id,
                            'type' => 'space',
                            'name' => $child->name,
                            'space_type' => $child->space_type,
                        ])->all(),
                    ])->all(),
                ])->all(),
            ])->all(),
        ]));
    }

    public function acs(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $today = now($organization->timezone ?: 'UTC')->startOfDay();

        $query = AcUnit::where('organization_id', $organization->id)
            ->with(['stateSnapshot', 'currentAssignment.device', 'space.floor.building.site', 'acModel', 'irProfileVersion']);

        if ($request->filled('space_id')) {
            $query->where('space_id', $request->integer('space_id'));
        }
        if ($request->filled('floor_id')) {
            $query->whereHas('space', fn ($q) => $q->where('floor_id', $request->integer('floor_id')));
        }
        if ($request->filled('building_id')) {
            $query->whereHas('space.floor', fn ($q) => $q->where('building_id', $request->integer('building_id')));
        }
        if ($request->filled('site_id')) {
            $query->whereHas('space.floor.building', fn ($q) => $q->where('site_id', $request->integer('site_id')));
        }
        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)->orWhere('asset_code', 'like', $term)
                    ->orWhereHas('space', fn ($s) => $s->where('name', 'like', $term))
                    ->orWhereHas('acModel', fn ($m) => $m->where('manufacturer', 'like', $term)->orWhere('model_name', 'like', $term));
            });
        }

        $acs = $query->orderBy('asset_code')->paginate(min(100, max(1, (int) $request->integer('per_page', 25))));

        $usage = $this->usage->baseQuery($organization, $today, $today)
            ->whereIn('usage.ac_unit_id', $acs->pluck('id'))
            ->pluck('usage.on_seconds', 'usage.ac_unit_id');

        return $this->ok($acs->through(fn (AcUnit $ac) => $this->acPayload($ac, (int) ($usage[$ac->id] ?? 0)))->toArray(), [
            'pagination' => [
                'page' => $acs->currentPage(),
                'per_page' => $acs->perPage(),
                'total' => $acs->total(),
            ],
        ]);
    }

    public function ac(Request $request, AcUnit $ac)
    {
        $this->authorizeAc($request, $ac);
        $ac->load(['stateSnapshot', 'currentAssignment.device', 'space.floor.building.site', 'acModel', 'irProfileVersion.profile.acModel']);
        $today = now($request->attributes->get('organization')->timezone ?: 'UTC')->startOfDay();
        $totals = $this->usage->totals($request->attributes->get('organization'), $today, $today, ['ac_id' => $ac->id]);

        return $this->ok(array_merge($this->acPayload($ac, $totals['on_seconds']), [
            'today_unknown_seconds' => $totals['unknown_seconds'],
            'effective_remote_policy' => $this->effectivePolicyPayload($ac),
            'recent_commands' => $ac->commands()->orderByDesc('issued_at')->limit(10)->get()->map(fn (AcCommand $command) => $this->commandPayload($command))->all(),
            'recent_activity' => ActivityLog::where('organization_id', $ac->organization_id)->where('ac_unit_id', $ac->id)
                ->orderByDesc('occurred_at')->limit(20)->get()->map(fn (ActivityLog $log) => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'source' => $log->source->value,
                    'result' => $log->result,
                    'occurred_at' => $log->occurred_at?->toIso8601String(),
                ])->all(),
        ]));
    }

    public function command(StoreAcCommandRequest $request, AcUnit $ac)
    {
        $this->authorizeAc($request, $ac);
        $command = $this->commands->create($request->attributes->get('organization'), $ac, $request->validated(), $request->user());

        return $this->ok($this->commandPayload($command), ['delivery' => 'queued_pending_device_poll'], 201);
    }

    public function commandStatus(Request $request, AcCommand $command)
    {
        if ($command->organization_id !== $request->attributes->get('organization')->id) {
            abort(404);
        }

        return $this->ok($this->commandPayload($command->load(['acUnit.stateSnapshot', 'device'])));
    }

    public function analytics(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $tz = $organization->timezone ?: 'UTC';
        $to = Carbon::parse($request->query('to', now($tz)->toDateString()), $tz)->startOfDay();
        $from = Carbon::parse($request->query('from', $to->copy()->subDays(6)->toDateString()), $tz)->startOfDay();

        if ($to->lt($from)) {
            [$from, $to] = [$to, $from];
        }

        $filters = array_filter([
            'site_id' => $request->integer('site_id') ?: null,
            'building_id' => $request->integer('building_id') ?: null,
            'floor_id' => $request->integer('floor_id') ?: null,
            'space_id' => $request->integer('space_id') ?: null,
            'ac_id' => $request->integer('ac_id') ?: null,
        ]);

        return $this->ok([
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totals' => $this->usage->totals($organization, $from, $to, $filters),
            'daily' => $this->usage->dailySeries($organization, $from, $to, $filters),
            'by_building' => $this->usage->byBuilding($organization, $from, $to, $filters)->values()->all(),
            'by_floor' => $this->usage->byFloor($organization, $from, $to, $filters)->values()->all(),
            'by_space' => $this->usage->bySpace($organization, $from, $to, $filters)->values()->all(),
            'by_ac' => $this->usage->byAc($organization, $from, $to, $filters)->values()->all(),
            'average_setpoint_c' => $this->usage->averageSetpoint($organization, $from, $to, $filters),
            'setpoint_history' => $this->usage->setpointSeries($organization, $from, $to, $filters),
            'units' => 'seconds (operating duration, not electricity)',
        ]);
    }

    public function activity(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $query = ActivityLog::where('organization_id', $organization->id)->with(['acUnit', 'user', 'device'])->orderByDesc('occurred_at');

        if ($request->filled('source') && EventSource::tryFrom((string) $request->query('source'))) {
            $query->where('source', $request->query('source'));
        }
        if ($request->filled('ac_id')) {
            $query->where('ac_unit_id', $request->integer('ac_id'));
        }
        if ($request->filled('q')) {
            $query->where('description', 'like', '%'.$request->query('q').'%');
        }

        $logs = $query->paginate(min(100, max(1, (int) $request->integer('per_page', 25))));

        return $this->ok($logs->through(fn (ActivityLog $log) => [
            'id' => $log->id,
            'ac' => $log->acUnit ? ['id' => $log->acUnit->id, 'name' => $log->acUnit->name, 'asset_code' => $log->acUnit->asset_code] : null,
            'source' => $log->source->value,
            'source_label' => $log->source->label(),
            'action' => $log->action,
            'description' => $log->description,
            'result' => $log->result,
            'actor' => $log->user?->name,
            'device' => $log->device?->name,
            'occurred_at' => $log->occurred_at?->toIso8601String(),
        ])->toArray(), ['pagination' => ['page' => $logs->currentPage(), 'per_page' => $logs->perPage(), 'total' => $logs->total()]]);
    }

    public function notifications(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $query = Notification::where('organization_id', $organization->id)->where('user_id', $request->user()->id)->orderByDesc('occurred_at');

        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->query('severity'));
        }

        $notifications = $query->paginate(min(100, max(1, (int) $request->integer('per_page', 25))));

        return $this->ok($notifications->through(fn (Notification $n) => [
            'id' => $n->id,
            'type' => $n->type,
            'severity' => $n->severity->value,
            'title' => $n->title,
            'message' => $n->message,
            'ac_unit_id' => $n->ac_unit_id,
            'action_url' => $n->action_url,
            'read' => $n->read_at !== null,
            'occurred_at' => $n->occurred_at?->toIso8601String(),
        ])->toArray(), ['pagination' => ['page' => $notifications->currentPage(), 'per_page' => $notifications->perPage(), 'total' => $notifications->total()]]);
    }

    public function markNotificationRead(Request $request, Notification $notification)
    {
        if ($notification->organization_id !== $request->attributes->get('organization')->id || $notification->user_id !== $request->user()->id) {
            abort(404);
        }

        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return $this->ok(['id' => $notification->id, 'read' => true]);
    }

    public function markAllNotificationsRead(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $count = Notification::where('organization_id', $organization->id)->where('user_id', $request->user()->id)
            ->whereNull('read_at')->update(['read_at' => now()]);

        return $this->ok(['updated' => $count]);
    }

    public function reports(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $reports = GeneratedReport::where('organization_id', $organization->id)->orderByDesc('created_at')->limit(50)->get();

        return $this->ok([
            'types' => config('hvac.report_types'),
            'reports' => $reports->map(fn (GeneratedReport $report) => [
                'id' => $report->id,
                'type' => $report->report_type,
                'title' => $report->title,
                'format' => $report->format,
                'status' => $report->status,
                'filters' => $report->filters,
                'created_at' => $report->created_at?->toIso8601String(),
                'completed_at' => $report->completed_at?->toIso8601String(),
            ])->all(),
        ]);
    }

    public function generateReport(GenerateReportRequest $request)
    {
        $data = $request->validated();
        $report = $this->reports->request(
            $request->attributes->get('organization'),
            $request->user(),
            $data['report_type'],
            array_filter([
                'from' => $data['from'], 'to' => $data['to'],
                'site_id' => $data['site_id'] ?? null, 'building_id' => $data['building_id'] ?? null,
                'floor_id' => $data['floor_id'] ?? null, 'space_id' => $data['space_id'] ?? null,
                'ac_id' => $data['ac_id'] ?? null,
            ], fn ($v) => $v !== null && $v !== ''),
            $data['format'],
        );

        return $this->ok($this->reportPayload($report), [], 201);
    }

    public function downloadReport(Request $request, GeneratedReport $report)
    {
        if ($report->organization_id !== $request->attributes->get('organization')->id) {
            abort(404);
        }

        if ($report->status !== 'completed' || ! $report->file_path || ! Storage::disk('local')->exists($report->file_path)) {
            return $this->error('REPORT_NOT_READY', 'The report file is not available.', [], 409);
        }

        return Storage::disk('local')->download($report->file_path, basename($report->file_path));
    }

    public function settings(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $policies = \App\Models\RemotePolicy::where('organization_id', $organization->id)->get();

        return $this->ok([
            'organization' => $organization->only(['id', 'name', 'slug', 'timezone', 'contact_email', 'contact_phone', 'address']),
            'profile' => $request->user()->only(['id', 'name', 'email', 'job_title', 'phone']),
            'notification_categories' => config('hvac.notification_categories'),
            'default_remote_policy' => config('hvac.default_remote_policy'),
            'policies' => $policies->map(fn ($policy) => [
                'scope_type' => $policy->scope_type->value,
                'scope_id' => $policy->scope_id,
                'policy_value' => $policy->policy_value->value,
                'version' => $policy->version,
                'updated_at' => $policy->updated_at?->toIso8601String(),
            ])->all(),
            'remote_policy_notice' => 'Software policy cannot physically block a handheld remote IR beam. It controls synchronization and reporting behavior only.',
        ]);
    }

    public function updatePolicy(UpdateRemotePolicyRequest $request)
    {
        $organization = $request->attributes->get('organization');
        $policy = $this->policies->setPolicy(
            $organization,
            PolicyScope::from($request->input('scope_type')),
            $request->integer('scope_id'),
            PolicyValue::from($request->input('policy_value')),
            $request->user(),
        );

        return $this->ok([
            'scope_type' => $policy->scope_type->value,
            'scope_id' => $policy->scope_id,
            'policy_value' => $policy->policy_value->value,
            'version' => $policy->version,
        ]);
    }

    private function authorizeAc(Request $request, AcUnit $ac): void
    {
        if ($ac->organization_id !== $request->attributes->get('organization')->id) {
            abort(404);
        }
        $this->authorize('view', $ac);
    }

    private function acPayload(AcUnit $ac, int $todaySeconds): array
    {
        $snapshot = $ac->stateSnapshot;
        $device = $ac->currentAssignment?->device;

        return [
            'id' => $ac->id,
            'name' => $ac->name,
            'asset_code' => $ac->asset_code,
            'status' => $ac->status,
            'location' => [
                'space_id' => $ac->space_id,
                'space' => $ac->space?->name,
                'floor' => $ac->space?->floor?->name,
                'building' => $ac->space?->floor?->building?->name,
                'site' => $ac->space?->floor?->building?->site?->name,
                'label' => $ac->locationLabel(),
            ],
            'model' => $ac->acModel ? [
                'manufacturer' => $ac->acModel->manufacturer,
                'model_name' => $ac->acModel->model_name,
                'remote_variant' => $ac->acModel->remote_variant,
            ] : null,
            'ir_profile' => $ac->irProfileVersion ? [
                'version_id' => $ac->irProfileVersion->id,
                'version' => $ac->irProfileVersion->version_number,
                'validation_status' => $ac->irProfileVersion->validation_status,
                'temperature_min_c' => $ac->irProfileVersion->temperature_min_c,
                'temperature_max_c' => $ac->irProfileVersion->temperature_max_c,
                'temperature_step_c' => (float) $ac->irProfileVersion->temperature_step_c,
            ] : null,
            'state' => $snapshot ? [
                'power_state' => $snapshot->power_state->value,
                'power_state_label' => $snapshot->power_state->label(),
                'temperature_setpoint_c' => $snapshot->temperature_setpoint_c,
                'quality' => $snapshot->quality->value,
                'source' => $snapshot->source,
                'observed_at' => $snapshot->observed_at?->toIso8601String(),
                'stale' => $snapshot->isStale(),
            ] : ['power_state' => 'unknown', 'power_state_label' => 'Unknown', 'temperature_setpoint_c' => null, 'quality' => 'unknown', 'stale' => true],
            'desired_state' => [
                'power_state' => $ac->desiredPowerState(),
                'temperature_setpoint_c' => $ac->desiredTemperatureSetpointC(),
            ],
            'device' => $device ? [
                'id' => $device->id,
                'name' => $device->name,
                'device_uid' => $device->device_uid,
                'connectivity' => $device->connectivity()->value,
                'last_seen_at' => $device->last_seen_at?->toIso8601String(),
                'effective_remote_policy' => $device->effective_remote_policy,
                'policy_synced_at' => $device->policy_synced_at?->toIso8601String(),
            ] : null,
            'today_on_seconds' => $todaySeconds,
            'can_control' => $ac->supportsProfileControl() && $device !== null,
            'links' => [
                'self' => route('api.manager.acs.show', $ac),
                'web' => route('acs.show', $ac),
                'control' => route('acs.control', $ac),
            ],
        ];
    }

    private function commandPayload(AcCommand $command): array
    {
        $command->loadMissing(['acUnit.stateSnapshot', 'device']);

        return [
            'id' => $command->id,
            'status' => $command->status->value,
            'status_label' => $command->status->label(),
            'is_pending' => $command->status->isPending(),
            'desired_state' => $command->desired_state,
            'issued_at' => $command->issued_at?->toIso8601String(),
            'expires_at' => $command->expires_at?->toIso8601String(),
            'published_at' => $command->published_at?->toIso8601String(),
            'received_at' => $command->received_at?->toIso8601String(),
            'transmission_at' => $command->transmission_at?->toIso8601String(),
            'completed_at' => $command->completed_at?->toIso8601String(),
            'failure_code' => $command->failure_code,
            'failure_message' => $command->failure_message,
            'last_reported' => $command->acUnit?->stateSnapshot ? [
                'power_state' => $command->acUnit->stateSnapshot->power_state->value,
                'temperature_setpoint_c' => $command->acUnit->stateSnapshot->temperature_setpoint_c,
                'quality' => $command->acUnit->stateSnapshot->quality->value,
                'observed_at' => $command->acUnit->stateSnapshot->observed_at?->toIso8601String(),
            ] : null,
        ];
    }

    private function effectivePolicyPayload(AcUnit $ac): array
    {
        $effective = $this->policies->effective($ac);

        return [
            'value' => $effective['value']->value,
            'source_scope' => $effective['source_scope'],
            'source_label' => $effective['source_label'],
            'version' => $effective['version'],
        ];
    }

    private function reportPayload(GeneratedReport $report): array
    {
        return [
            'id' => $report->id,
            'type' => $report->report_type,
            'title' => $report->title,
            'format' => $report->format,
            'status' => $report->status,
            'created_at' => $report->created_at?->toIso8601String(),
            'completed_at' => $report->completed_at?->toIso8601String(),
            'download_url' => $report->status === 'completed' ? route('api.manager.reports.download', $report) : null,
        ];
    }
}
