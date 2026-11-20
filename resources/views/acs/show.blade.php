@extends('layouts.app')

@section('page-title', 'AC Details & Status')
@section('page-subtitle', 'Complete view of configuration, state, commands and recent activity')

@section('content')
    @php
        $snapshot = $ac->stateSnapshot;
        $stale = $snapshot?->isStale() ?? true;
        $device = $ac->currentAssignment?->device;
        $stateColor = $snapshot?->power_state === \App\Enums\PowerState::KnownOn && ! $stale
            ? 'text-[#0d8c4e]'
            : 'text-ink';

        $formatHistoryValue = function ($value, string $field) {
            if (is_array($value)) {
                $value = $value[$field] ?? $value['value'] ?? reset($value);
            }

            if ($field === 'power_state') {
                return match ((string) $value) {
                    'known_on' => 'ON',
                    'known_off' => 'OFF',
                    'unknown' => 'Unknown',
                    'stale' => 'Stale',
                    default => $value === null ? '—' : ucfirst((string) $value),
                };
            }

            if ($field === 'temperature_setpoint_c') {
                return is_numeric($value) ? $value.'°C' : '—';
            }

            return $value === null || $value === '' ? '—' : ucfirst((string) $value);
        };

        $tabs = [
            'overview' => ['label' => 'Overview', 'icon' => 'dashboard'],
            'history' => ['label' => 'History', 'icon' => 'activity'],
            'usage' => ['label' => 'Usage', 'icon' => 'analytics'],
            'settings' => ['label' => 'Settings', 'icon' => 'settings'],
        ];
    @endphp

    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <a href="{{ route('acs.index') }}" class="btn btn-ghost btn-sm"><x-icon name="arrow-left" :size="13" /> Back to inventory</a>
        <div class="flex items-center gap-2">
            <a href="{{ route('acs.show', ['ac' => $ac, 'tab' => 'settings']) }}" class="btn btn-secondary btn-sm"><x-icon name="edit" :size="14" /> Edit</a>
            <a href="{{ route('acs.control', $ac) }}" class="btn btn-primary btn-sm {{ $ac->supportsProfileControl() && $device ? '' : 'pointer-events-none opacity-50' }}"><x-icon name="power" :size="14" /> Control panel</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-pad">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <x-icon name="ac" :size="25" :stroke="1.6" />
                    </span>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="truncate text-[21px] font-bold tracking-[-0.025em] text-ink">{{ $ac->asset_code }}</h1>
                            <x-status-badge :state="$snapshot?->power_state" :stale="$stale" :quality="$snapshot?->quality?->value" />
                            <span class="badge {{ $ac->status === 'active' ? 'badge-info' : 'badge-unknown' }}">{{ ucfirst($ac->status) }}</span>
                        </div>
                        <p class="mt-0.5 truncate text-[12px] text-ink-soft">{{ $ac->name }}</p>
                        <p class="mt-1 flex flex-wrap items-center gap-1 text-[10.5px] text-muted">
                            <x-icon name="locations" :size="11" />
                            @if ($ac->space?->floor?->building?->site)
                                <a href="{{ route('locations.index', ['type' => 'site', 'id' => $ac->space->floor->building->site->id]) }}" class="hover:text-blue-700 hover:underline">{{ $ac->space->floor->building->site->name }}</a>
                                <x-icon name="chevron-right" :size="9" />
                            @endif
                            @if ($ac->space?->floor?->building)
                                <a href="{{ route('locations.index', ['type' => 'building', 'id' => $ac->space->floor->building->id]) }}" class="hover:text-blue-700 hover:underline">{{ $ac->space->floor->building->name }}</a>
                                <x-icon name="chevron-right" :size="9" />
                            @endif
                            @if ($ac->space?->floor)
                                <a href="{{ route('locations.index', ['type' => 'floor', 'id' => $ac->space->floor->id]) }}" class="hover:text-blue-700 hover:underline">{{ $ac->space->floor->name }}</a>
                                <x-icon name="chevron-right" :size="9" />
                            @endif
                            @if ($ac->space)
                                <a href="{{ route('locations.index', ['type' => 'space', 'id' => $ac->space->id]) }}" class="font-medium text-ink-soft hover:text-blue-700 hover:underline">{{ $ac->space->name }}</a>
                            @else
                                <span>Unassigned location</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <x-connectivity-badge :status="$ac->connectivity()" />
                    <span class="text-[10px] text-muted">Last update {{ \App\Support\Format::relative($snapshot?->observed_at) }}</span>
                </div>
            </div>
        </div>

        <div class="border-t border-divider px-4 pt-3 md:px-[18px]">
            <nav class="tab-list w-fit" aria-label="AC detail tabs">
                @foreach ($tabs as $key => $meta)
                    <a href="{{ route('acs.show', ['ac' => $ac, 'tab' => $key]) }}"
                       class="tab {{ $tab === $key ? 'tab-active' : '' }}">
                        {{ $meta['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    @if ($tab === 'overview')
        <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="stat-card">
                <p class="filter-label">Reported state</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none {{ $stateColor }}">
                    <x-icon name="power" :size="18" />
                    {{ $snapshot?->power_state?->shortLabel() ?? 'Unknown' }}
                </p>
                <p class="mt-1 text-[10px] text-muted">
                    {{ $stale ? 'Stale — not confirmed OFF' : 'Quality: '.($snapshot?->quality?->label() ?? 'Unknown') }}
                </p>
            </div>
            <div class="stat-card">
                <p class="filter-label">Temperature setpoint</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none text-ink">
                    <x-icon name="thermometer" :size="18" class="text-blue-500" />
                    {{ $snapshot?->temperature_setpoint_c !== null ? $snapshot->temperature_setpoint_c.'°C' : 'Unknown' }}
                </p>
                <p class="mt-1 text-[10px] text-muted">Target temperature, not room temperature</p>
            </div>
            <div class="stat-card">
                <p class="filter-label">Today’s ON duration</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none text-ink">
                    <x-icon name="clock" :size="18" class="text-[#3a9c78]" />
                    {{ \App\Support\Format::duration($usage['today']['on_seconds'], true) }}
                </p>
                <p class="mt-1 text-[10px] text-muted">Operating time, not kWh</p>
            </div>
            <div class="stat-card">
                <p class="filter-label">Device connection</p>
                <p class="mt-1.5 flex items-center gap-2 text-[20px] font-bold leading-none text-ink">
                    <x-icon :name="$ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'wifi' : 'wifi-off'" :size="18" class="{{ $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'text-[#0d8c4e]' : 'text-muted' }}" />
                    {{ $ac->connectivity()->label() }}
                </p>
                <p class="mt-1 text-[10px] text-muted">Last seen {{ $device?->last_seen_at?->diffForHumans(short: true) ?? 'never' }}</p>
            </div>
        </div>

        @if ($stale || $snapshot?->power_state === null || $snapshot?->power_state === \App\Enums\PowerState::Unknown)
            <div class="mt-3 flex items-start gap-2 rounded-xl border border-[#f6deac] bg-warning-soft/70 px-3.5 py-2.5 text-[11px] text-[#8a5a05]">
                <x-icon name="alert" :size="14" class="mt-0.5" />
                <p>
                    <strong>This AC’s state is {{ $stale ? 'stale' : 'unknown' }}.</strong>
                    It is not counted as OFF. Device connectivity does not prove the AC is running.
                    @if ($device) The last report was received {{ \App\Support\Format::relative($snapshot?->observed_at) }}. @endif
                </p>
            </div>
        @endif

        <div class="mt-4 grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Last-known configuration</h2>
                            <p class="card-subtitle">Reported by the device; may lag behind a physical remote action</p>
                        </div>
                        <span class="badge {{ $snapshot?->quality?->value === 'inferred' ? 'badge-warning' : 'badge-info' }}">{{ $snapshot?->quality?->label() ?? 'Unknown' }}</span>
                    </div>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 p-4 text-[11.5px] md:p-[18px]">
                        <div><dt class="text-muted">Power</dt><dd class="mt-0.5 font-semibold text-ink">{{ $snapshot?->power_state?->label() ?? 'Unknown' }}</dd></div>
                        <div><dt class="text-muted">Setpoint</dt><dd class="mt-0.5 font-semibold text-ink">{{ $snapshot?->temperature_setpoint_c !== null ? $snapshot->temperature_setpoint_c.'°C' : 'Unknown' }}</dd></div>
                        <div><dt class="text-muted">Operating mode</dt><dd class="mt-0.5 font-semibold text-ink">{{ $snapshot?->operating_mode ? ucfirst($snapshot->operating_mode) : 'Not reported' }}</dd></div>
                        <div><dt class="text-muted">Fan setting</dt><dd class="mt-0.5 font-semibold text-ink">{{ $snapshot?->fan_setting ? ucfirst($snapshot->fan_setting) : 'Not reported' }}</dd></div>
                        <div><dt class="text-muted">Swing</dt><dd class="mt-0.5 font-semibold text-ink">{{ $snapshot?->swing_setting ? ucfirst($snapshot->swing_setting) : 'Not reported' }}</dd></div>
                        <div><dt class="text-muted">Observed at</dt><dd class="mt-0.5 font-semibold text-ink">{{ \App\Support\Format::datetime($snapshot?->observed_at, $timezone) }}</dd></div>
                        <div><dt class="text-muted">Received at</dt><dd class="mt-0.5 font-semibold text-ink">{{ \App\Support\Format::datetime($snapshot?->received_at, $timezone) }}</dd></div>
                        <div><dt class="text-muted">State source</dt><dd class="mt-0.5 font-semibold text-ink">{{ $snapshot?->source ? ucfirst(str_replace('_', ' ', $snapshot->source)) : '—' }}</dd></div>
                    </dl>
                    <div class="border-t border-divider bg-[#fbfdff] px-4 py-2.5 text-[10px] leading-relaxed text-muted">
                        This reading is the AC’s last-known state. A command transmission does not physically confirm the AC changed state.
                    </div>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Assigned ESP32 device</h2>
                            <p class="card-subtitle">One device can control exactly one AC at a time</p>
                        </div>
                        @if ($device)<x-connectivity-badge :status="$device->connectivity()" />@endif
                    </div>
                    @if ($device)
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 p-4 text-[11.5px] md:p-[18px]">
                            <div><dt class="text-muted">Device</dt><dd class="mt-0.5 font-semibold text-ink">{{ $device->name }}</dd></div>
                            <div><dt class="text-muted">Device ID</dt><dd class="mt-0.5 font-mono text-[10.5px] font-semibold text-ink">{{ $device->device_uid }}</dd></div>
                            <div><dt class="text-muted">Hardware</dt><dd class="mt-0.5 font-semibold text-ink">{{ $device->hardware_model ?: '—' }}</dd></div>
                            <div><dt class="text-muted">Firmware</dt><dd class="mt-0.5 font-semibold text-ink">{{ $device->firmware_version ?: '—' }}</dd></div>
                            <div><dt class="text-muted">Last seen</dt><dd class="mt-0.5 font-semibold text-ink">{{ \App\Support\Format::datetime($device->last_seen_at, $timezone) }}</dd></div>
                            <div><dt class="text-muted">Remote policy</dt><dd class="mt-0.5 font-semibold text-ink">{{ ucfirst($device->effective_remote_policy ?? 'inherit') }} (v{{ $device->policy_version }})</dd></div>
                        </dl>
                        <div class="border-t border-divider px-4 py-2.5">
                            <form method="POST" action="{{ route('acs.device.assign', $ac) }}" class="flex flex-wrap items-end gap-2">
                                @csrf
                                <div class="min-w-[180px] flex-1">
                                    <label class="label mb-1" for="detail-device">Reassign device</label>
                                    <select id="detail-device" name="device_id" class="select h-[34px] py-0 text-[11.5px]">
                                        <option value="">Unassign device</option>
                                        @foreach ($availableDevices as $candidate)
                                            <option value="{{ $candidate->id }}" @selected($candidate->id === $device->id)>
                                                {{ $candidate->name }} · {{ $candidate->device_uid }}
                                                @if ($candidate->currentAssignment && $candidate->currentAssignment->ac_unit_id !== $ac->id)
                                                    (currently {{ $candidate->currentAssignment->acUnit?->asset_code }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary btn-sm h-[34px]">Save assignment</button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 md:p-[18px]">
                            <p class="text-[11.5px] text-ink-soft">No ESP32 is assigned. Remote control is unavailable until a validated device is assigned.</p>
                            <form method="POST" action="{{ route('acs.device.assign', $ac) }}" class="mt-3 flex flex-wrap items-end gap-2">
                                @csrf
                                <div class="min-w-[180px] flex-1">
                                    <label class="label mb-1" for="detail-device-empty">Assign device</label>
                                    <select id="detail-device-empty" name="device_id" class="select h-[34px] py-0 text-[11.5px]">
                                        <option value="">Select a device…</option>
                                        @foreach ($availableDevices as $candidate)
                                            <option value="{{ $candidate->id }}">{{ $candidate->name }} · {{ $candidate->device_uid }}@if ($candidate->currentAssignment) (assigned to {{ $candidate->currentAssignment->acUnit?->asset_code }})@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm h-[34px]">Assign</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Recent commands</h2>
                            <p class="card-subtitle">Queue and transmission facts, newest first</p>
                        </div>
                        <a href="{{ route('acs.show', ['ac' => $ac, 'tab' => 'history']) }}" class="btn btn-ghost btn-sm">History <x-icon name="arrow-right" :size="12" /></a>
                    </div>
                    @forelse ($ac->commands as $command)
                        <div class="flex items-start gap-2.5 border-b border-divider px-4 py-2.5 last:border-0">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full
                                {{ $command->status === \App\Enums\CommandStatus::Failed || $command->status === \App\Enums\CommandStatus::Expired ? 'bg-danger-soft text-danger' : ($command->status === \App\Enums\CommandStatus::Completed ? 'bg-success-soft text-[#0d8c4e]' : 'bg-blue-50 text-blue-600') }}">
                                <x-icon :name="$command->status === \App\Enums\CommandStatus::Completed ? 'check' : ($command->status->isPending() ? 'clock' : 'alert')" :size="11" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <p class="text-[11.5px] font-semibold text-ink">{{ $command->status->label() }}</p>
                                    <span class="badge badge-unknown">{{ ucfirst(str_replace('_', ' ', $command->command_type)) }}</span>
                                </div>
                                <p class="mt-0.5 text-[10px] text-muted">
                                    @if (! empty($command->desired_state['power']))
                                        Power {{ strtoupper($command->desired_state['power']) }}
                                    @endif
                                    @if (isset($command->desired_state['temperature_setpoint_c']))
                                        · Setpoint {{ $command->desired_state['temperature_setpoint_c'] }}°C
                                    @endif
                                    · {{ $command->requestedBy?->name ?? 'System' }}
                                </p>
                                @if ($command->failure_message)
                                    <p class="mt-0.5 text-[10px] text-danger">{{ $command->failure_message }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 text-right text-[9.5px] text-muted">{{ \App\Support\Format::relative($command->issued_at) }}</span>
                        </div>
                    @empty
                        <x-empty-state icon="command" title="No commands yet" message="Commands issued from the control panel will appear here." />
                    @endforelse
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Physical remote policy</h2>
                            <p class="card-subtitle">Nearest explicit scope wins</p>
                        </div>
                        <span class="badge {{ $effectivePolicy['value']->value === 'enabled' ? 'badge-online' : 'badge-off' }}">{{ $effectivePolicy['value']->label() }}</span>
                    </div>
                    <div class="p-4 md:p-[18px]">
                        <p class="text-[11px] text-ink-soft">Effective source: <strong class="text-ink">{{ $effectivePolicy['source_label'] }}</strong> · version {{ $effectivePolicy['version'] }}</p>
                        <p class="mt-0.5 text-[10px] text-muted">AC override: {{ $explicitPolicy ? $explicitPolicy->policy_value->label() : 'Inherit' }}</p>
                        <form method="POST" action="{{ route('acs.policy.update', $ac) }}" class="mt-3 flex flex-wrap items-end gap-2">
                            @csrf
                            <div class="min-w-[150px] flex-1">
                                <label class="label mb-1" for="ac-policy">AC-level policy</label>
                                <select id="ac-policy" name="policy_value" class="select h-[34px] py-0 text-[11.5px]">
                                    <option value="inherit" @selected(! $explicitPolicy || $explicitPolicy->policy_value->value === 'inherit')>Inherit from parent</option>
                                    <option value="enabled" @selected($explicitPolicy?->policy_value->value === 'enabled')>Enabled</option>
                                    <option value="disabled" @selected($explicitPolicy?->policy_value->value === 'disabled')>Disabled</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-secondary btn-sm h-[34px]">Apply</button>
                        </form>
                        <p class="mt-2 text-[9.5px] leading-relaxed text-muted">Software cannot block a handheld remote’s direct IR beam. Policy affects synchronization, reporting and device behaviour only.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Recent activity</h2>
                    <p class="card-subtitle">Portal, physical remote, device and system events</p>
                </div>
                <a href="{{ route('acs.show', ['ac' => $ac, 'tab' => 'history']) }}" class="btn btn-ghost btn-sm">Full history <x-icon name="arrow-right" :size="12" /></a>
            </div>
            @forelse ($ac->activityLogs as $log)
                <div class="flex flex-wrap items-center gap-3 border-b border-divider px-4 py-2.5 last:border-0">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                        {{ $log->result === 'failure' ? 'bg-danger-soft text-danger' : ($log->source === \App\Enums\EventSource::PhysicalRemote ? 'bg-warning-soft text-[#b77405]' : ($log->result === 'success' ? 'bg-success-soft text-[#0d8c4e]' : 'bg-blue-50 text-blue-600')) }}">
                        <x-icon :name="$log->source === \App\Enums\EventSource::PhysicalRemote ? 'wifi' : ($log->result === 'failure' ? 'alert' : 'check')" :size="12" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[11.5px] font-medium text-ink">{{ $log->description }}</p>
                        <p class="text-[10px] text-muted">{{ $log->source->label() }} · {{ $log->user?->name ?? ($log->device?->name ?? 'System') }}</p>
                    </div>
                    <span class="badge {{ $log->result === 'failure' ? 'badge-danger' : ($log->result === 'pending' ? 'badge-warning' : 'badge-info') }}">{{ ucfirst($log->result ?? 'info') }}</span>
                    <span class="w-28 text-right text-[9.5px] text-muted">{{ \App\Support\Format::datetime($log->occurred_at, $timezone) }}</span>
                </div>
            @empty
                <x-empty-state icon="activity" title="No activity yet" message="Commands, device reports and remote events will appear here." />
            @endforelse
        </div>
    @elseif ($tab === 'history')
        <div class="mt-4 grid gap-4 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Activity timeline</h2>
                        <p class="card-subtitle">Latest 20 events for this AC</p>
                    </div>
                    <x-icon name="activity" :size="16" class="text-muted" />
                </div>
                <div class="p-4 md:p-[18px]">
                    @if ($ac->activityLogs->isEmpty())
                        <x-empty-state icon="activity" title="No history yet" message="Events will appear as commands and reports are processed." />
                    @else
                        <div class="timeline">
                            @foreach ($ac->activityLogs as $log)
                                <div class="timeline-item relative">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-[11.5px] font-semibold text-ink">{{ $log->description }}</p>
                                        <span class="badge {{ $log->result === 'failure' ? 'badge-danger' : ($log->result === 'pending' ? 'badge-warning' : 'badge-info') }}">{{ ucfirst($log->result ?? 'info') }}</span>
                                    </div>
                                    <p class="mt-0.5 text-[10px] text-muted">
                                        {{ $log->source->label() }} · {{ $log->user?->name ?? ($log->device?->name ?? 'System') }} · {{ \App\Support\Format::datetime($log->occurred_at, $timezone) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">State change history</h2>
                            <p class="card-subtitle">Append-only facts; unknown/stale is never converted to OFF</p>
                        </div>
                    </div>
                    @if ($ac->stateHistory->isEmpty())
                        <x-empty-state icon="clock" title="No state changes" message="State changes reported by the device will be recorded here." />
                    @else
                        <div class="table-scroll">
                            <table class="table">
                                <thead><tr><th>Observed</th><th>Field</th><th>Value</th><th>Source</th><th>Quality</th></tr></thead>
                                <tbody>
                                    @foreach ($ac->stateHistory as $change)
                                        <tr>
                                            <td class="text-[10.5px] text-muted">{{ \App\Support\Format::datetime($change->observed_at, $timezone) }}</td>
                                            <td class="font-medium text-ink">{{ ucfirst(str_replace('_', ' ', $change->state_field)) }}</td>
                                            <td class="font-semibold text-ink">{{ $formatHistoryValue($change->new_value, $change->state_field) }}</td>
                                            <td>{{ $change->source ? ucfirst(str_replace('_', ' ', $change->source)) : '—' }}</td>
                                            <td><span class="badge {{ $change->quality?->value === 'reported' ? 'badge-info' : 'badge-warning' }}">{{ ucfirst($change->quality?->value ?? 'unknown') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Device events</h2>
                            <p class="card-subtitle">Latest 15 messages received from the ESP32</p>
                        </div>
                    </div>
                    @if ($ac->deviceEvents->isEmpty())
                        <x-empty-state icon="wifi" title="No device events" message="Device heartbeats and reports are stored here." />
                    @else
                        <div class="table-scroll">
                            <table class="table">
                                <thead><tr><th>Occurred</th><th>Type</th><th>Source</th><th>Processing</th></tr></thead>
                                <tbody>
                                    @foreach ($ac->deviceEvents as $event)
                                        <tr>
                                            <td class="text-[10.5px] text-muted">{{ \App\Support\Format::datetime($event->device_occurred_at, $timezone) }}</td>
                                            <td class="font-medium text-ink">{{ str_replace(['.', '_'], [' ', ' '], $event->event_type) }}</td>
                                            <td>{{ $event->source ? ucfirst(str_replace('_', ' ', $event->source)) : '—' }}</td>
                                            <td><span class="badge {{ $event->processing_status === 'processed' ? 'badge-online' : 'badge-unknown' }}">{{ ucfirst($event->processing_status ?? 'received') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif ($tab === 'usage')
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <x-metric-card label="Today" :value="\App\Support\Format::duration($usage['today']['on_seconds'], true)" icon="clock" tone="blue" hint="Known ON duration · operating time, not kWh" />
            <x-metric-card label="Last 7 days" :value="\App\Support\Format::duration($usage['week']['on_seconds'], true)" icon="calendar" tone="cyan" hint="Includes only known ON intervals" />
            <x-metric-card label="Last 30 days" :value="\App\Support\Format::duration($usage['month']['on_seconds'], true)" icon="analytics" tone="green" hint="Unknown/stale gaps are excluded" />
        </div>

        <div class="mt-4 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">ON duration — last 14 days</h2>
                        <p class="card-subtitle">Known operating time derived from state transitions</p>
                    </div>
                    <span class="badge badge-info">Hours</span>
                </div>
                <div class="chart-box mt-3">
                    @php
                        $usageChart = [
                            "type" => "bar",
                            "labels" => collect($usage["daily"])->pluck("label")->all(),
                            "datasets" => [[
                                "label" => "Known ON hours",
                                "data" => collect($usage["daily"])->map(fn ($row) => round($row["seconds"] / 3600, 2))->all(),
                                "color" => "#299ff2",
                                "fill" => false,
                            ]],
                            "showLegend" => false,
                            "tooltipSuffix" => "h",
                        ];
                    @endphp
                    <canvas data-chart='@json($usageChart)'></canvas>
                </div>
                <p class="mt-2 text-[9.5px] leading-relaxed text-muted">No electricity consumption is inferred. ON duration is not kWh.</p>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Daily breakdown</h2>
                        <p class="card-subtitle">14-day window</p>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Known ON</th><th class="text-right">Share</th></tr></thead>
                        <tbody>
                            @php $dailyTotal = max(1, collect($usage['daily'])->sum('seconds')); @endphp
                            @foreach (array_reverse($usage['daily']) as $day)
                                <tr>
                                    <td class="font-medium text-ink">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d M Y') }}</td>
                                    <td>{{ \App\Support\Format::duration($day['seconds']) }}</td>
                                    <td class="text-right text-muted">{{ \App\Support\Format::percent($day['seconds'], $dailyTotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 card card-pad">
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                    <p class="filter-label">Unknown / stale time (30 days)</p>
                    <p class="mt-1 text-[18px] font-bold text-ink">{{ \App\Support\Format::duration($usage['month']['unknown_seconds'], true) }}</p>
                    <p class="mt-0.5 text-[9.5px] text-muted">Not counted as OFF</p>
                </div>
                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                    <p class="filter-label">Incomplete days (30 days)</p>
                    <p class="mt-1 text-[18px] font-bold text-ink">{{ $usage['month']['incomplete_rows'] }}</p>
                    <p class="mt-0.5 text-[9.5px] text-muted">Days with provisional intervals</p>
                </div>
                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                    <p class="filter-label">Analysis note</p>
                    <p class="mt-1 text-[10.5px] leading-relaxed text-ink-soft">ON duration reflects known state transitions. Gaps while the device was offline or the state was unknown are excluded from OFF time.</p>
                </div>
            </div>
        </div>
    @else
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">AC profile & configuration</h2>
                        <p class="card-subtitle">Identity, location and IR profile assignment</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('acs.update', $ac) }}" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 md:p-[18px]">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="label" for="edit-ac-name">Display name</label>
                        <input id="edit-ac-name" name="name" value="{{ $ac->name }}" class="input" required maxlength="120">
                    </div>
                    <div>
                        <label class="label" for="edit-ac-code">Asset ID</label>
                        <input id="edit-ac-code" name="asset_code" value="{{ $ac->asset_code }}" class="input" required maxlength="60">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="edit-ac-space">Space</label>
                        <select id="edit-ac-space" name="space_id" class="select" required>
                            @foreach ($spaces as $space)
                                <option value="{{ $space->id }}" @selected($space->id === $ac->space_id)>{{ $space->floor?->building?->site?->name }} · {{ $space->floor?->building?->name }} · {{ $space->floor?->name }} · {{ $space->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="edit-ac-model">Manufacturer / model</label>
                        <select id="edit-ac-model" name="ac_model_id" class="select" required>
                            @foreach ($acModels as $model)
                                <option value="{{ $model->id }}" @selected($model->id === $ac->ac_model_id)>{{ $model->manufacturer }} {{ $model->model_name }} ({{ $model->remote_variant }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="edit-ac-profile">IR profile version</label>
                        <select id="edit-ac-profile" name="ir_profile_version_id" class="select">
                            <option value="">No profile assigned</option>
                            @foreach ($profileVersions as $version)
                                <option value="{{ $version->id }}" @selected($version->id === $ac->ir_profile_version_id)>{{ $version->profile?->acModel?->manufacturer }} {{ $version->profile?->acModel?->model_name }} · v{{ $version->version_number }} · {{ ucfirst($version->validation_status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="edit-ac-serial">Serial number</label>
                        <input id="edit-ac-serial" name="serial_number" value="{{ $ac->serial_number }}" class="input" maxlength="80">
                    </div>
                    <div>
                        <label class="label" for="edit-ac-installed">Installation date</label>
                        <input id="edit-ac-installed" name="installation_date" type="date" value="{{ $ac->installation_date?->format('Y-m-d') }}" class="input">
                    </div>
                    <div>
                        <label class="label" for="edit-ac-status">Status</label>
                        <select id="edit-ac-status" name="status" class="select">
                            @foreach (['active', 'inactive', 'maintenance', 'retired'] as $status)
                                <option value="{{ $status }}" @selected($ac->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="edit-ac-notes">Notes</label>
                        <textarea id="edit-ac-notes" name="notes" rows="2" class="input" maxlength="1000">{{ $ac->notes }}</textarea>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-divider pt-3 sm:col-span-2">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">IR capability profile</h2>
                            <p class="card-subtitle">Limits come from the assigned profile version</p>
                        </div>
                        <span class="badge {{ $ac->irProfileVersion?->isValidated() ? 'badge-online' : 'badge-warning' }}">{{ $ac->irProfileVersion?->isValidated() ? 'Validated' : 'Not validated' }}</span>
                    </div>
                    @if ($ac->irProfileVersion)
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 p-4 text-[11.5px] md:p-[18px]">
                            <div><dt class="text-muted">Profile</dt><dd class="mt-0.5 font-semibold text-ink">{{ $ac->irProfileVersion->profile?->name ?? 'IR profile' }}</dd></div>
                            <div><dt class="text-muted">Version</dt><dd class="mt-0.5 font-semibold text-ink">v{{ $ac->irProfileVersion->version_number }}</dd></div>
                            <div><dt class="text-muted">Temperature range</dt><dd class="mt-0.5 font-semibold text-ink">{{ $ac->irProfileVersion->temperature_min_c }}–{{ $ac->irProfileVersion->temperature_max_c }}°C</dd></div>
                            <div><dt class="text-muted">Step</dt><dd class="mt-0.5 font-semibold text-ink">{{ (float) $ac->irProfileVersion->temperature_step_c }}°C</dd></div>
                        </dl>
                        <div class="flex flex-wrap gap-1.5 border-t border-divider px-4 py-2.5">
                            @foreach (($ac->irProfileVersion->capabilities ?? []) as $capability => $enabled)
                                <span class="badge {{ $enabled ? 'badge-online' : 'badge-unknown' }}">{{ ucfirst(str_replace('_', ' ', $capability)) }}</span>
                            @endforeach
                        </div>
                    @else
                        <x-empty-state icon="command" title="No IR profile assigned" message="Control is disabled until a validated IR profile version is attached to this AC." />
                    @endif
                </div>

                <div class="card card-pad">
                    <h2 class="text-[12.5px] font-semibold text-ink">Record</h2>
                    <dl class="mt-2.5 space-y-2 text-[11px]">
                        <div class="flex justify-between gap-2"><dt class="text-muted">Manufacturer</dt><dd class="font-medium text-ink">{{ $ac->acModel?->manufacturer ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Model</dt><dd class="font-medium text-ink">{{ $ac->acModel?->model_name ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Remote variant</dt><dd class="font-medium text-ink">{{ $ac->acModel?->remote_variant ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Installed</dt><dd class="font-medium text-ink">{{ $ac->installation_date ? $ac->installation_date->format('d M Y') : '—' }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Registered</dt><dd class="font-medium text-ink">{{ \App\Support\Format::datetime($ac->created_at, $timezone) }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    @endif
@endsection
