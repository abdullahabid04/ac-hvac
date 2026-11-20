@extends('layouts.app')

@section('page-title', 'Developer Simulator')
@section('page-subtitle', 'Emit device events through the real ingestion pipeline (local environment only)')

@section('content')
    <div class="alert alert-warning mb-4">
        <x-icon name="alert" :size="17" class="mt-0.5" />
        <div>
            <p class="font-semibold">Development-only tool. Simulated events are illustrative, not production evidence.</p>
            <p class="mt-0.5">Auto-response is {{ $simulatorEnabled ? 'enabled' : 'disabled' }} (<code>HVAC_SIMULATOR_AUTO_RESPOND</code>). Every action below uses the same authentication-independent ingestion service as a real ESP32 message, so idempotency, state projection and notifications are exercised.</p>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Emit an event</h2>
                    <p class="card-subtitle">Choose a device and action</p>
                </div>
                <span class="badge badge-info">Local only</span>
            </div>
            <form method="POST" action="{{ route('dev.simulator.action') }}" class="space-y-3 p-4 md:p-[18px]" x-data="{ action: 'state_report' }">
                @csrf
                <div>
                    <label class="label" for="sim-device">Device</label>
                    <select id="sim-device" name="device_id" class="select" required>
                        <option value="">Select a device…</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}">
                                {{ $device->name }} · {{ $device->device_uid }}
                                @if ($device->acUnit()) — {{ $device->acUnit()->asset_code }} @else — unassigned @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label" for="sim-action">Action</label>
                        <select id="sim-action" name="action" class="select" x-model="action" required>
                            <option value="state_report">State report</option>
                            <option value="heartbeat">Heartbeat</option>
                            <option value="remote_action">Physical remote action</option>
                            <option value="unknown_signal">Unknown IR signal</option>
                            <option value="complete_command">Complete a queued command</option>
                            <option value="mark_offline">Mark device offline</option>
                            <option value="reconnect">Reconnect device</option>
                        </select>
                    </div>
                    <div x-show="action === 'state_report' || action === 'remote_action'" x-cloak>
                        <label class="label" for="sim-power">Power</label>
                        <select id="sim-power" name="power" class="select">
                            <option value="">Not included</option>
                            <option value="on">ON</option>
                            <option value="off">OFF</option>
                        </select>
                    </div>
                </div>

                <div x-show="action === 'state_report' || action === 'remote_action'" x-cloak>
                    <label class="label" for="sim-setpoint">Temperature setpoint (°C) <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                    <input id="sim-setpoint" type="number" name="temperature_setpoint_c" min="10" max="35" class="input" placeholder="e.g. 24">
                </div>

                <div x-show="action === 'complete_command'" x-cloak>
                    <label class="label" for="sim-command">Queued command</label>
                    <select id="sim-command" name="command_id" class="select">
                        <option value="">Select a pending command…</option>
                        @foreach ($recentCommands->whereIn('status.value', ['created', 'published', 'received', 'transmission_attempted']) as $command)
                            <option value="{{ $command->id }}">
                                {{ $command->acUnit?->asset_code }} · {{ $command->status->label() }} · {{ $command->issued_at?->format('H:i:s') }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[9.5px] text-muted">Completing a command records transmission success. It does not prove the physical AC changed state.</p>
                </div>

                <div class="flex justify-end border-t border-divider pt-3">
                    <button type="submit" class="btn btn-primary"><x-icon name="sparkles" :size="14" /> Emit event</button>
                </div>
            </form>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Devices</h2>
                    <p class="card-subtitle">{{ $devices->count() }} ESP32 device{{ $devices->count() === 1 ? '' : 's' }}</p>
                </div>
            </div>
            <div class="table-scroll">
                <table class="table">
                    <thead><tr><th>Device</th><th>AC</th><th>Connectivity</th><th>Last seen</th><th class="text-right">Quick actions</th></tr></thead>
                    <tbody>
                        @forelse ($devices as $device)
                            <tr>
                                <td>
                                    <span class="block font-semibold text-ink">{{ $device->name }}</span>
                                    <span class="block font-mono text-[9px] text-muted">{{ $device->device_uid }}</span>
                                </td>
                                <td>
                                    @if ($device->acUnit())
                                        <a href="{{ route('acs.show', $device->acUnit()) }}" class="font-medium text-ink hover:text-blue-700">{{ $device->acUnit()->asset_code }}</a>
                                        <span class="block text-[9px] text-muted">{{ $device->acUnit()->space?->name ?? 'Unassigned space' }}</span>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td><x-connectivity-badge :status="$device->connectivity()" /></td>
                                <td class="text-[10px] text-muted">{{ \App\Support\Format::relative($device->last_seen_at) }}</td>
                                <td>
                                    <div class="flex flex-wrap justify-end gap-1">
                                        @foreach ([['heartbeat', 'Ping'], ['mark_offline', 'Offline'], ['reconnect', 'Reconnect'], ['unknown_signal', 'Unknown IR']] as [$action, $label])
                                            <form method="POST" action="{{ route('dev.simulator.action') }}">
                                                @csrf
                                                <input type="hidden" name="device_id" value="{{ $device->id }}">
                                                <input type="hidden" name="action" value="{{ $action }}">
                                                <button type="submit" class="btn btn-ghost btn-sm">{{ $label }}</button>
                                            </form>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No devices seeded. Run the database seeder.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 card overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="card-title">Recent commands</h2>
                <p class="card-subtitle">Latest 10 commands issued in this organization</p>
            </div>
        </div>
        <div class="table-scroll">
            <table class="table">
                <thead><tr><th>Issued</th><th>AC</th><th>Desired state</th><th>Status</th><th>Expires</th><th>Command ID</th></tr></thead>
                <tbody>
                    @forelse ($recentCommands as $command)
                        <tr>
                            <td class="whitespace-nowrap text-[10.5px] text-muted">{{ \App\Support\Format::datetime($command->issued_at, $organization->timezone ?: 'UTC') }}</td>
                            <td class="font-semibold text-ink">{{ $command->acUnit?->asset_code ?? '—' }}</td>
                            <td class="text-[11px]">
                                @if (! empty($command->desired_state['power']))
                                    Power {{ strtoupper($command->desired_state['power']) }}
                                @endif
                                @if (isset($command->desired_state['temperature_setpoint_c']))
                                    · {{ $command->desired_state['temperature_setpoint_c'] }}°C
                                @endif
                            </td>
                            <td><span class="badge {{ $command->status->isPending() ? 'badge-warning' : ($command->status === \App\Enums\CommandStatus::Completed ? 'badge-online' : 'badge-danger') }}">{{ $command->status->label() }}</span></td>
                            <td class="whitespace-nowrap text-[10.5px] text-muted">{{ \App\Support\Format::datetime($command->expires_at, $organization->timezone ?: 'UTC') }}</td>
                            <td class="font-mono text-[9px] text-muted">{{ $command->id }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No commands issued yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 card card-pad">
        <h2 class="card-title">Production device contract</h2>
        <p class="mt-2 text-[10.5px] leading-relaxed text-ink-soft">
            Real devices authenticate with per-device bearer tokens and call <code class="rounded bg-ice-100 px-1 py-0.5 font-mono text-[10px]">POST /api/device/heartbeat</code>,
            <code class="rounded bg-ice-100 px-1 py-0.5 font-mono text-[10px]">POST /api/device/events</code> and
            <code class="rounded bg-ice-100 px-1 py-0.5 font-mono text-[10px]">GET /api/device/commands</code>.
            Event ingestion is idempotent by event UID. See <code class="rounded bg-ice-100 px-1 py-0.5 font-mono text-[10px]">docs/implementation/DEVICE_API.md</code> once generated.
        </p>
    </div>
@endsection
