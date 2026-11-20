@php
    $currentView = $filters['view'];
    $sortUrl = function (string $column) use ($filters) {
        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $filters['sort'] === $column && $filters['direction'] === 'asc' ? 'desc' : 'asc',
        ]);
    };

    $onCount = $acs->filter(fn ($ac) => $ac->stateSnapshot?->power_state === \App\Enums\PowerState::KnownOn && ! $ac->stateSnapshot?->isStale())->count();
    $offCount = $acs->filter(fn ($ac) => $ac->stateSnapshot?->power_state === \App\Enums\PowerState::KnownOff && ! $ac->stateSnapshot?->isStale())->count();
    $unknownCount = max(0, $acs->count() - $onCount - $offCount);
    $staleCount = $acs->filter(fn ($ac) => $ac->stateSnapshot?->isStale() && $ac->stateSnapshot?->power_state !== \App\Enums\PowerState::Unknown)->count();
@endphp

<div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-muted">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-ink-soft"><span class="font-semibold text-ink">{{ number_format($acs->total()) }}</span> air conditioner{{ $acs->total() === 1 ? '' : 's' }}</span>

        <span class="summary-chip {{ $filters['state'] === 'on' ? 'summary-chip-active' : '' }}">
            <x-icon name="power" :size="11" class="text-[#0d8c4e]" />
            <span>{{ $onCount }} ON</span>
        </span>
        <span class="summary-chip {{ $filters['state'] === 'off' ? 'summary-chip-active' : '' }}">
            <x-icon name="power" :size="11" class="text-neutral" />
            <span>{{ $offCount }} OFF</span>
        </span>
        <span class="summary-chip {{ $filters['state'] === 'unknown' || $filters['state'] === 'stale' ? 'summary-chip-active' : '' }}">
            <x-icon name="alert" :size="11" class="text-[#b77405]" />
            <span>{{ $unknownCount + $staleCount }} Unknown / Stale</span>
        </span>
    </div>

    <div class="flex items-center gap-2">
        <label for="sort-select" class="text-[10px] uppercase tracking-[0.05em] text-muted">Sort</label>
        <form method="GET" action="{{ route('acs.index') }}" id="sort-form">
            @foreach (request()->except(['sort', 'direction', 'page']) as $key => $value)
                @if (is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
            @endforeach
            <select id="sort-select" name="sort" class="border-0 bg-transparent py-0 pr-6 text-[11px] font-semibold text-ink-soft outline-none" onchange="this.form.submit()">
                <option value="asset_code" @selected($filters['sort'] === 'asset_code')>Asset code</option>
                <option value="name" @selected($filters['sort'] === 'name')>Name</option>
                <option value="duration" @selected($filters['sort'] === 'duration')>Today’s ON duration</option>
                <option value="setpoint" @selected($filters['sort'] === 'setpoint')>Setpoint</option>
                <option value="last_update" @selected($filters['sort'] === 'last_update')>Last update</option>
            </select>
            <input type="hidden" name="direction" value="{{ $filters['direction'] }}">
        </form>
        <a href="{{ $sortUrl($filters['sort']) }}" class="btn btn-ghost btn-sm px-1.5" title="Toggle direction">
            <x-icon :name="$filters['direction'] === 'asc' ? 'chevron-down' : 'chevron-right'" :size="13" />
        </a>
    </div>
</div>

@if ($acs->isEmpty())
    <div class="card">
        <x-empty-state icon="ac" title="No air conditioners match" message="Adjust your search or filters, or register a new AC and assign it to a space.">
            <button type="button" class="btn btn-primary btn-sm" @click="addOpen = true"><x-icon name="plus" :size="13" /> Register AC</button>
        </x-empty-state>
    </div>
@elseif ($currentView === 'table')
    <div class="card overflow-hidden">
        <div class="space-y-2">
            @foreach ($acs as $ac)
                        @php
                            $snapshot = $ac->stateSnapshot;
                            $stale = $snapshot?->isStale() ?? true;
                            $device = $ac->currentAssignment?->device;
                            $profile = $ac->irProfileVersion;
                            $canControl = $ac->supportsProfileControl() && $device;
                            $powerVal = $snapshot?->power_state?->value ?? 'unknown';
                            $qualityVal = $snapshot?->quality?->value ?? 'unknown';
                            $setpointVal = $snapshot?->temperature_setpoint_c ?? null;
                            $minVal = $profile?->temperature_min_c ?? 16;
                            $maxVal = $profile?->temperature_max_c ?? 30;
                            $stepVal = (float) ($profile?->temperature_step_c ?? 1);
                            $cmdUrl = route('acs.commands.store', $ac);
                            $pollUrl = route('commands.show', ['command' => '__COMMAND__']);
                            $obsAt = $snapshot?->observed_at?->toIso8601String() ?? '';
                            $deviceStatusClass = $device ? 'ac-list-chip-small-online' : 'ac-list-chip-small-unknown';
                            $deviceStatusText = $device ? $device->connectivity()->label() : 'Unknown';
                        @endphp
                        <article x-data="acControl" x-init="config = {
                    acId: {{ $ac->id }},
                    commandUrl: '{{ $cmdUrl }}',
                    commandPollUrl: '{{ $pollUrl }}',
                    power: '{{ $powerVal }}',
                    setpoint: {{ $setpointVal }},
                    min: {{ $minVal }},
                    max: {{ $maxVal }},
                    step: {{ $stepVal }},
                    canControl: {{ $canControl ? 'true' : 'false' }},
                    deviceOnline: {{ $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'true' : 'false' }},
                    observedAt: '{{ $obsAt }}',
                    quality: '{{ $qualityVal }}'
                }; init()"
                    class="list-ac-card border border-line/85 rounded-xl bg-white p-3 flex flex-col md:flex-row items-center justify-between gap-3 transition hover:border-blue-300 hover:shadow-[0_4px_12px_rgba(8,117,201,0.15)]">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[110px]">
                        <a href="{{ route('acs.show', $ac) }}" class="text-[12px] font-bold text-ink truncate hover:text-blue-700">{{ $ac->asset_code }}</a>
                        <span class="block text-[9px] text-muted truncate">{{ $ac->name }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[140px]">
                        <span class="block max-w-[150px] truncate text-[9.5px] text-ink-soft">{{ $ac->space?->name ?? 'Unassigned' }}</span>
                        @if ($ac->space?->floor)
                            <span class="block text-[9px] text-muted truncate">{{ $ac->space->floor->name }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-3 min-w-[120px]">
                        <span class="block text-[9px] text-ink-soft">{{ $ac->acModel?->manufacturer ?? '—' }}</span>
                        <span class="block text-[9px] text-muted truncate">{{ $ac->acModel?->model_name ?? 'Model not recorded' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center sm:justify-center gap-2 sm:gap-3 min-w-[70px]">
                        <button type="button"
                            class="rocker rocker-sm {{ $canControl && $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? '' : 'opacity-70' }}"
                            :data-on="isOn ? 'true' : 'false'"
                            :data-pending="pending ? 'true' : 'false'"
                            :disabled="! config.canControl || pending"
                            aria-label="AC power rocker. ON is at the top, OFF is at the bottom."
                            @click="setPower(isOn ? 'off' : 'on')">
                            <span class="rocker-thumb" :class="isOn ? 'rocker-thumb-on' : 'rocker-thumb-off'"></span>
                            <span class="rocker-position rocker-on">
                                <x-icon name="power" :size="11" :stroke="2" />
                                ON
                            </span>
                            <span class="rocker-position rocker-off">
                                <x-icon name="power" :size="11" :stroke="1.6" />
                                OFF
                            </span>
                        </button>
                        <div class="flex items-center gap-1 mt-1 sm:mt-0">
                            <button type="button"
                                class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                :disabled="! canDecrease || pending"
                                :class="(! canDecrease || pending) ? 'opacity-40' : ''"
                                @click="changeSetpoint(-1)"
                                aria-label="Decrease setpoint">
                                <span class="text-[13px] font-light leading-none">−</span>
                            </button>
                            <span class="font-semibold text-ink">{{ $setpointVal !== null ? $setpointVal : '—' }}</span><span class="text-[10px] text-muted">°C</span>
                            <button type="button"
                                class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                :disabled="! canIncrease || pending"
                                :class="(! canIncrease || pending) ? 'opacity-40' : ''"
                                @click="changeSetpoint(1)"
                                aria-label="Increase setpoint">
                                <span class="text-[13px] font-light leading-none">+</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[110px]">
                        <span class="ac-list-chip-small {{ $deviceStatusClass }}">{{ $deviceStatusText }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[80px] text-right sm:text-right">
                        <span class="text-[9px] text-muted">{{ \App\Support\Format::relative($snapshot?->observed_at) }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 min-w-[70px]">
                        <a href="{{ route('acs.show', $ac) }}" class="flex items-center gap-1.5 btn btn-ghost btn-sm" title="View details">
                            <x-icon name="info" class="text-[11px]" /> <span class="text-[9.5px]">Details</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@else
    <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        @foreach ($acs as $ac)
            @php
                $snapshot = $ac->stateSnapshot;
                $stale = $snapshot?->isStale() ?? true;
                $device = $ac->currentAssignment?->device;
                $profile = $ac->irProfileVersion;
                $canControl = $ac->supportsProfileControl() && $device;
                $onSeconds = (int) ($todayUsage[$ac->id] ?? 0);
                $cmdUrl = route('acs.commands.store', $ac);
                $pollUrl = route('commands.show', ['command' => '__COMMAND__']);
                $obsAt = $snapshot?->observed_at?->toIso8601String() ?? '';
                $powerVal = $snapshot?->power_state?->value ?? 'unknown';
                $qualityVal = $snapshot?->quality?->value ?? 'unknown';
                $setpointVal = $snapshot?->temperature_setpoint_c ?? 'null';
                $minVal = $profile?->temperature_min_c ?? 16;
                $maxVal = $profile?->temperature_max_c ?? 30;
                $stepVal = (float) ($profile?->temperature_step_c ?? 1);
                $deviceStatusClass = $device ? 'ac-list-chip-small-online' : 'ac-list-chip-small-unknown';
                $deviceStatusText = $device ? $device->connectivity()->label() : 'Unknown';
            @endphp
            <article x-data="acControl" x-init="config = {
                acId: {{ $ac->id }},
                commandUrl: '{{ $cmdUrl }}',
                commandPollUrl: '{{ $pollUrl }}',
                power: '{{ $powerVal }}',
                setpoint: {{ $setpointVal }},
                min: {{ $minVal }},
                max: {{ $maxVal }},
                step: {{ $stepVal }},
                canControl: {{ $canControl ? 'true' : 'false' }},
                deviceOnline: {{ $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? 'true' : 'false' }},
                observedAt: '{{ $obsAt }}',
                quality: '{{ $qualityVal }}'
            }; init()"
                class="ac-card-grid group overflow-hidden transition hover:border-blue-300 hover:shadow-[0_10px_24px_-18px_rgba(8,117,201,0.4)]">
                <div class="p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('acs.show', $ac) }}" class="block truncate text-[12.5px] font-bold tracking-[-0.01em] text-ink hover:text-blue-700">{{ $ac->asset_code }}</a>
                            <span class="block text-[9px] text-muted text-xs uppercase tracking-[0.05em]">{{ $ac->id }}</span>
                        </div>
                        <a href="{{ route('acs.show', $ac) }}" class="text-[9px] text-muted hover:text-blue-700" title="View details">
                            <x-icon name="info" :size="12" />
                        </a>
                    </div>

                    <div class="mt-2 flex items-center gap-1.5 text-[9.5px] text-ink-soft">
                        <x-icon name="space" :size="10" class="text-[#8fb6d8]" />
                        <span class="truncate">{{ $ac->space?->name ?? 'Unassigned' }}</span>
                        @if ($ac->space?->floor)
                            <span class="text-muted">· {{ $ac->space->floor->name }}</span>
                        @endif
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-1">
                        <span class="rounded-md bg-[#f3f8fc] px-1.5 py-0.5 text-[9px] font-medium text-ink-soft">{{ $ac->acModel?->manufacturer ?? 'Unknown' }}</span>
                        <span class="rounded-md bg-[#f3f8fc] px-1.5 py-0.5 text-[9px] font-medium text-ink-soft">{{ $ac->acModel?->model_name ?? 'No model' }}</span>
                    </div>

                    {{-- Controls: rocker + temperature --}}
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button type="button"
                                class="rocker rocker-sm {{ $canControl && $ac->connectivity() === \App\Enums\ConnectivityStatus::Online ? '' : 'opacity-70' }}"
                                :data-on="isOn ? 'true' : 'false'"
                                :data-pending="pending ? 'true' : 'false'"
                                :disabled="! config.canControl || pending"
                                aria-label="AC power rocker. ON is at the top, OFF is at the bottom."
                                @click="setPower(isOn ? 'off' : 'on')">
                                <span class="rocker-thumb" :class="isOn ? 'rocker-thumb-on' : 'rocker-thumb-off'"></span>
                                <span class="rocker-position rocker-on">
                                    <x-icon name="power" :size="13" :stroke="2" />
                                    ON
                                </span>
                                <span class="rocker-position rocker-off">
                                    <x-icon name="power" :size="13" :stroke="1.6" />
                                    OFF
                                </span>
                            </button>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="temp-dial-sm" :style="'--dial: ' + dialPercent + '%'">
                                <div class="temp-dial-inner">
                                    <p class="text-[8px] font-semibold uppercase tracking-[0.12em] text-muted">Setpoint</p>
                                    <p class="mt-0.5 temp-text-sm">
                                        <span x-text="(requestedSetpoint !== null && requestedSetpoint !== undefined) ? requestedSetpoint : '—'"></span><span class="text-[14px] align-top">°C</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 mt-1">
                                <button type="button"
                                    class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                    :disabled="! canDecrease || pending"
                                    :class="(! canDecrease || pending) ? 'opacity-40' : ''"
                                    @click="changeSetpoint(-1)"
                                    aria-label="Decrease setpoint">
                                    <span class="text-[14px] font-light leading-none">−</span>
                                </button>
                                <button type="button"
                                    class="btn btn-secondary btn-icon shrink-0 h-6 w-6"
                                    :disabled="! canIncrease || pending"
                                    :class="(! canIncrease || pending) ? 'opacity-40' : ''"
                                    @click="changeSetpoint(1)"
                                    aria-label="Increase setpoint">
                                    <span class="text-[14px] font-light leading-none">+</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-2">
                        <span class="ac-list-chip-small {{ $deviceStatusClass }}">{{ $deviceStatusText }}</span>
                        <span class="power-status-chip {{ $powerVal === 'known_on' ? 'bg-success-soft text-[#0d8c4e]' : ($powerVal === 'known_off' ? 'bg-[#f0f4f8] text-neutral' : 'bg-warning-soft text-[#b77405]') }} text-[9px] font-semibold px-2 py-0.5 rounded-full">
                            {{ $stale ? 'Stale ' : '' }}{{ $powerVal === 'known_on' ? 'ON' : ($powerVal === 'known_off' ? 'OFF' : 'Unknown') }}
                        </span>
                        <span class="text-[9px] text-muted">{{ \App\Support\Format::relative($snapshot?->observed_at) }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-1 border-t border-divider bg-[#fbfdff] px-3 py-1.5">
                    <a href="{{ route('acs.show', $ac) }}" class="flex-1 flex items-center justify-center gap-1.5 btn btn-ghost btn-sm" title="View details">
                        <x-icon name="info" class="text-[12px]" /> <span class="text-[9.5px]">Details</span>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
@endif

@if ($acs->hasPages())
    <div class="mt-3">{{ $acs->links() }}</div>
@endif

<p class="mt-2 text-[9px] leading-relaxed text-muted">
    Setpoint is the AC target temperature, not measured room temperature. Device connectivity is separate from AC power state. Unknown setpoint is shown as “—”, never 0°C.
</p>
