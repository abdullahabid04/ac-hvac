@extends('layouts.app')

@section('page-title', 'Overview')
@section('page-subtitle', 'Real-time operational snapshot across your air conditioning estate')

@section('content')
    <form method="GET" action="{{ route('overview') }}" class="mb-4 flex flex-wrap items-center gap-2">
        <div class="flex items-center gap-2 rounded-xl border border-line bg-white px-2.5 py-1.5 shadow-sm">
            <x-icon name="locations" :size="14" class="text-muted" />
            <select name="site_id" class="border-0 bg-transparent p-0 pr-6 text-[11.5px] font-semibold text-ink-soft outline-none focus:ring-0">
                <option value="">All sites</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" @selected($siteId === $site->id)>{{ $site->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2 rounded-xl border border-line bg-white px-2.5 py-1.5 shadow-sm">
            <x-icon name="calendar" :size="14" class="text-muted" />
            <select name="range" class="border-0 bg-transparent p-0 pr-6 text-[11.5px] font-semibold text-ink-soft outline-none focus:ring-0">
                <option value="1" @selected($range === 1)>Today</option>
                <option value="7" @selected($range === 7)>Last 7 days</option>
                <option value="30" @selected($range === 30)>Last 30 days</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary btn-sm">Apply filters</button>
        <a href="{{ route('overview') }}" class="btn btn-ghost btn-sm">Reset</a>
        <span class="ml-auto hidden items-center gap-1.5 text-[10.5px] text-muted sm:flex">
            <x-icon name="clock" :size="12" /> Updated {{ now($organization->timezone ?: 'UTC')->format('H:i') }} · {{ $organization->timezone ?: 'UTC' }}
        </span>
    </form>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        <x-metric-card label="Total ACs" :value="number_format($stats['total'])" icon="ac" tone="blue" :hint="$sites->count().' sites in scope'" :href="route('acs.index', array_filter(['site_id' => $siteId]))" />
        <x-metric-card label="Currently ON" :value="number_format($stats['on'])" icon="power" tone="green" :hint="\App\Support\Format::percent($stats['on'], max(1, $stats['total'])).' of ACs'" />
        <x-metric-card label="Known OFF" :value="number_format($stats['off'])" icon="power" tone="slate" :hint="\App\Support\Format::percent($stats['off'], max(1, $stats['total'])).' of ACs'" />
        <x-metric-card label="Offline devices" :value="number_format($stats['offline_devices'])" icon="wifi-off" tone="amber" :hint="($stats['unknown_devices'] + $stats['stale']).' unknown / stale'" />
        <x-metric-card label="Today's ON Duration" :value="\App\Support\Format::duration($todayOnSeconds, true)" icon="clock" tone="cyan" hint="Operating time, not kWh" />
    </div>

    @if ($stats['unknown'] > 0 || $stats['stale'] > 0)
        <div class="mt-3 flex flex-wrap items-center gap-2 rounded-xl border border-[#f6deac] bg-warning-soft/70 px-3.5 py-2 text-[11px] text-[#8a5a05]">
            <x-icon name="alert" :size="14" />
            <span class="font-semibold">{{ $stats['unknown'] + $stats['stale'] }} AC{{ ($stats['unknown'] + $stats['stale']) === 1 ? '' : 's' }}</span>
            have unknown or stale state. These are <strong>not</strong> counted as OFF.
            <a href="{{ route('acs.index', ['state' => 'unknown']) }}" class="ml-auto font-semibold underline underline-offset-2">Review unknown states →</a>
        </div>
    @endif

    <div class="mt-4 grid gap-4 xl:grid-cols-[0.78fr_1.22fr]">
        <div class="card card-pad">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="card-title">AC state distribution</h2>
                    <p class="card-subtitle">Last reported state; stale is shown separately</p>
                </div>
                <a href="{{ route('acs.index') }}" class="btn btn-ghost btn-sm">Inventory <x-icon name="arrow-right" :size="12" /></a>
            </div>
            <div class="mt-2 grid items-center gap-3 sm:grid-cols-[1.02fr_0.98fr]">
                <div class="chart-box-sm" style="height:170px">
                    @php
                        $stateChart = [
                            "type" => "doughnut",
                            "labels" => collect($chart["state"])->pluck("label")->all(),
                            "values" => collect($chart["state"])->pluck("value")->all(),
                            "colors" => collect($chart["state"])->pluck("color")->all(),
                            "cutout" => "72%",
                        ];
                    @endphp
                    <canvas data-chart='@json($stateChart)'></canvas>
                </div>
                <ul class="space-y-2.5">
                    @foreach ($chart['state'] as $slice)
                        <li class="flex items-center gap-2 text-[11.5px]">
                            <span class="h-2 w-2 rounded-full" style="background: {{ $slice['color'] }}"></span>
                            <span class="flex-1 text-ink-soft">{{ $slice['label'] }}</span>
                            <span class="font-semibold text-ink">{{ $slice['value'] }}</span>
                            <span class="w-9 text-right text-[10px] text-muted">{{ \App\Support\Format::percent($slice['value'], max(1, $stats['total'])) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card card-pad">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="card-title">Operating hours trend</h2>
                    <p class="card-subtitle">Known ON duration · last {{ $range }} day{{ $range === 1 ? '' : 's' }}</p>
                </div>
                <a href="{{ route('analytics.index') }}" class="btn btn-ghost btn-sm">Analytics <x-icon name="arrow-right" :size="12" /></a>
            </div>
            <div class="chart-box mt-3">
                @php
                    $trendChart = [
                        "type" => "line",
                        "labels" => collect($series)->pluck("label")->all(),
                        "datasets" => [[
                            "label" => "Known ON hours",
                            "data" => collect($series)->map(fn ($point) => round($point["seconds"] / 3600, 1))->all(),
                            "color" => "#1689e8",
                            "fillColor" => "rgba(41,159,242,0.10)",
                            "fill" => true,
                        ]],
                        "showLegend" => false,
                        "tooltipSuffix" => "h",
                    ];
                @endphp
                <canvas data-chart='@json($trendChart)'></canvas>
            </div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">ACs by building</h2>
                    <p class="card-subtitle">Reported state and today’s known operating time</p>
                </div>
                <a href="{{ route('locations.index') }}" class="btn btn-ghost btn-sm">Locations <x-icon name="arrow-right" :size="12" /></a>
            </div>
            @if ($byBuilding->isEmpty())
                <x-empty-state icon="building" title="No buildings in scope" message="Create a site and building to start grouping air conditioners." />
            @else
                <div class="table-scroll">
                    <table class="table">
                        <thead>
                            <tr><th>Building</th><th>Total</th><th>ON</th><th>OFF</th><th>Unknown</th><th>Today</th><th class="w-28">State mix</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($byBuilding as $row)
                                <tr>
                                    <td class="font-semibold text-ink">{{ $row['name'] }}</td>
                                    <td>{{ $row['total'] }}</td>
                                    <td class="text-[#0d8c4e]">{{ $row['on'] }}</td>
                                    <td>{{ $row['off'] }}</td>
                                    <td class="text-muted">{{ $row['unknown'] }}</td>
                                    <td class="font-semibold text-ink">{{ \App\Support\Format::duration($row['today_seconds'], true) }}</td>
                                    <td>
                                        <div class="flex h-1.5 overflow-hidden rounded-full bg-[#edf3f8]">
                                            @if ($row['on'] > 0)<span class="bg-success" style="width: {{ ($row['on'] / max(1, $row['total'])) * 100 }}%"></span>@endif
                                            @if ($row['off'] > 0)<span class="bg-neutral" style="width: {{ ($row['off'] / max(1, $row['total'])) * 100 }}%"></span>@endif
                                            @if ($row['unknown'] > 0)<span class="bg-[#c6d6e4]" style="width: {{ ($row['unknown'] / max(1, $row['total'])) * 100 }}%"></span>@endif
                                        </div>
                                    </td>
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
                    <h2 class="card-title">Active alerts</h2>
                    <p class="card-subtitle">Unread warnings and alerts for you</p>
                </div>
                <a href="{{ route('notifications.index', ['tab' => 'alert']) }}" class="btn btn-ghost btn-sm">View all</a>
            </div>
            @forelse ($alerts as $alert)
                <a href="{{ $alert->action_url ?: route('notifications.index') }}" class="flex gap-3 border-b border-divider px-4 py-3 transition last:border-0 hover:bg-blue-50/60">
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $alert->severity->value === 'alert' ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-[#b77405]' }}">
                        <x-icon :name="$alert->severity->value === 'alert' ? 'alert' : 'info'" :size="14" />
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-[12px] font-semibold text-ink">{{ $alert->title }}</span>
                        <span class="mt-0.5 block truncate text-[10.5px] text-ink-soft">{{ $alert->acUnit?->locationLabel() ?? $alert->message }}</span>
                        <span class="mt-1 block text-[9.5px] font-medium uppercase tracking-[0.05em] text-muted">{{ $alert->occurred_at?->diffForHumans(short: true) }}</span>
                    </span>
                </a>
            @empty
                <x-empty-state icon="check-circle" title="No active alerts" message="Device and command alerts will appear here." />
            @endforelse
            <div class="border-t border-divider bg-[#fbfdff] px-4 py-2.5">
                <p class="text-[10px] leading-relaxed text-muted">Device online status does not prove an AC is running. IR transmission success does not prove physical execution.</p>
            </div>
        </div>
    </div>

    <div class="mt-4 card overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="card-title">Recent activity</h2>
                <p class="card-subtitle">Portal, physical remote, device and system events</p>
            </div>
            <a href="{{ route('activity.index') }}" class="btn btn-ghost btn-sm">Activity history <x-icon name="arrow-right" :size="12" /></a>
        </div>
        @forelse ($recentActivity as $log)
            <div class="flex flex-wrap items-center gap-3 border-b border-divider px-4 py-2.5 last:border-0 sm:flex-nowrap">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                    {{ $log->result === 'failure' ? 'bg-danger-soft text-danger' : ($log->source->value === 'physical_remote' ? 'bg-warning-soft text-[#b77405]' : ($log->result === 'success' ? 'bg-success-soft text-[#0d8c4e]' : 'bg-blue-50 text-blue-600')) }}">
                    <x-icon :name="$log->source->value === 'physical_remote' ? 'wifi' : ($log->result === 'failure' ? 'alert' : 'check')" :size="13" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[12px] font-medium text-ink">{{ $log->description }}</p>
                    <p class="text-[10px] text-muted">
                        {{ $log->acUnit?->asset_code ?? 'Organization' }}
                        @if ($log->acUnit) · {{ $log->acUnit->locationLabel() }} @endif
                        · {{ $log->source->label() }}
                    </p>
                </div>
                <span class="badge {{ $log->result === 'failure' ? 'badge-danger' : ($log->result === 'pending' ? 'badge-warning' : 'badge-info') }}">{{ ucfirst($log->result ?? 'info') }}</span>
                <span class="w-20 text-right text-[10px] text-muted">{{ $log->occurred_at?->diffForHumans(short: true) }}</span>
            </div>
        @empty
            <x-empty-state icon="activity" title="No activity yet" message="Commands, device reports and remote events will appear here." />
        @endforelse
    </div>
@endsection
