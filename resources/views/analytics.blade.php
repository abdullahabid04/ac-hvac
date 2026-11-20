@extends('layouts.app')

@section('page-title', 'Usage & Analytics')
@section('page-subtitle', 'Known ON duration, setpoint trends and usage breakdowns — never kWh')

@section('content')
    @php
        $queryBase = request()->except(['period', 'from', 'to', 'tab', 'page']);
        $periodUrl = fn (string $value) => route('analytics.index', array_merge($queryBase, ['period' => $value, 'tab' => $tab]));
        $tabUrl = fn (string $value) => route('analytics.index', array_merge(request()->except(['tab', 'page']), ['tab' => $value]));
        $periodLabel = match ($period) {
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            '30d' => 'Last 30 days',
            'custom' => 'Custom range',
            default => 'Last 7 days',
        };
        $setpointLabels = collect($setpointSeries)->map(fn ($row) => \Illuminate\Support\Carbon::parse($row['at'])->setTimezone($timezone)->format('d M H:i'))->all();
        $totalOn = max(1, $totals['on_seconds']);
        $tabs = [
            'overview' => 'Usage Overview',
            'setpoint' => 'Temperature Trend',
            'location' => 'Location-wise',
            'ac' => 'AC-wise',
        ];
    @endphp

    <form method="GET" action="{{ route('analytics.index') }}" class="mb-4 space-y-3">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="tab-list">
                @foreach (['today' => 'Today', 'yesterday' => 'Yesterday', '7d' => '7 days', '30d' => '30 days', 'custom' => 'Custom'] as $value => $label)
                    @if ($value === 'custom')
                        <button type="submit" name="period" value="custom" class="tab {{ $period === 'custom' ? 'tab-active' : '' }}">{{ $label }}</button>
                    @else
                        <a href="{{ $periodUrl($value) }}" class="tab {{ $period === $value ? 'tab-active' : '' }}">{{ $label }}</a>
                    @endif
                @endforeach
            </div>

            <div class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="label mb-1" for="analytics-site">Site</label>
                    <select id="analytics-site" name="site_id" class="select h-[34px] w-[130px] py-0 text-[11.5px]">
                        <option value="">All sites</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" @selected($filters['site_id'] === $site->id)>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label mb-1" for="analytics-building">Building</label>
                    <select id="analytics-building" name="building_id" class="select h-[34px] w-[130px] py-0 text-[11.5px]">
                        <option value="">All buildings</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}" @selected($filters['building_id'] === $building->id)>{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label mb-1" for="analytics-floor">Floor</label>
                    <select id="analytics-floor" name="floor_id" class="select h-[34px] w-[115px] py-0 text-[11.5px]">
                        <option value="">All floors</option>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}" @selected($filters['floor_id'] === $floor->id)>{{ $floor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label mb-1" for="analytics-ac">AC</label>
                    <select id="analytics-ac" name="ac_id" class="select h-[34px] w-[135px] py-0 text-[11.5px]">
                        <option value="">All ACs</option>
                        @foreach ($acs as $unit)
                            <option value="{{ $unit->id }}" @selected($filters['ac_id'] === $unit->id)>{{ $unit->asset_code }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" name="period" value="{{ $period }}" class="btn btn-primary btn-sm h-[34px]"><x-icon name="filter" :size="13" /> Apply</button>
                <a href="{{ route('analytics.index') }}" class="btn btn-ghost btn-sm h-[34px]">Reset</a>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-line bg-white px-3 py-2 shadow-sm">
            <div class="flex flex-wrap items-center gap-3 text-[11px] text-ink-soft">
                <span class="inline-flex items-center gap-1.5 font-semibold text-ink"><x-icon name="calendar" :size="13" class="text-blue-500" /> {{ $periodLabel }}</span>
                <span>{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</span>
                <span class="text-muted">{{ $timezone }}</span>
                <span class="inline-flex items-center gap-1.5"><x-icon name="ac" :size="12" class="text-muted" /> {{ $scopedAcCount }} AC{{ $scopedAcCount === 1 ? '' : 's' }} in scope</span>
            </div>
            <div class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="label mb-0.5" for="analytics-from">From</label>
                    <input id="analytics-from" type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="input h-[30px] w-[130px] py-0 text-[11px]">
                </div>
                <div>
                    <label class="label mb-0.5" for="analytics-to">To</label>
                    <input id="analytics-to" type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="input h-[30px] w-[130px] py-0 text-[11px]">
                </div>
                <button type="submit" name="period" value="custom" class="btn btn-secondary btn-sm h-[30px]">Custom range</button>
            </div>
        </div>
    </form>

    <div class="mb-4 flex items-center gap-1 overflow-x-auto rounded-xl border border-line bg-white p-1 shadow-sm">
        @foreach ($tabs as $key => $label)
            <a href="{{ $tabUrl($key) }}" class="whitespace-nowrap rounded-lg px-3.5 py-1.5 text-[11.5px] font-semibold transition {{ $tab === $key ? 'bg-blue-600 text-white shadow-[0_6px_14px_-10px_rgba(22,137,232,0.9)]' : 'text-ink-soft hover:bg-blue-50 hover:text-blue-700' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if ($totals['incomplete_rows'] > 0 || $totals['unknown_seconds'] > 0)
        <div class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-[#f6deac] bg-warning-soft/70 px-3.5 py-2 text-[11px] text-[#8a5a05]">
            <x-icon name="alert" :size="14" />
            <span><strong>{{ \App\Support\Format::duration($totals['unknown_seconds'], true) }}</strong> of unknown/interrupted time and <strong>{{ $totals['incomplete_rows'] }}</strong> incomplete aggregate row{{ $totals['incomplete_rows'] === 1 ? '' : 's' }} are in this range.</span>
            <span class="text-[10px]">Unknown time is never presented as OFF or as zero consumption.</span>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        <x-metric-card label="Total ON duration" :value="\App\Support\Format::duration($totals['on_seconds'], true)" icon="clock" tone="blue" hint="Known operating time · not kWh" />
        <x-metric-card label="Average per AC" :value="\App\Support\Format::duration($averagePerAc, true)" icon="analytics" tone="cyan" hint="Across {{ max(1, $totals['ac_count']) }} reporting ACs" />
        <x-metric-card label="ACs in scope" :value="number_format($scopedAcCount)" icon="ac" tone="green" hint="{{ $totals['ac_count'] }} have usage data" />
        <x-metric-card label="Average setpoint" :value="$averageSetpoint !== null ? $averageSetpoint.'°C' : 'Unknown'" icon="thermometer" tone="amber" hint="Target temperature, not room temperature" />
        <x-metric-card label="Unknown / stale time" :value="\App\Support\Format::duration($totals['unknown_seconds'], true)" icon="alert" tone="red" hint="Excluded from ON and OFF" />
    </div>

    @if ($tab === 'overview')
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Daily ON duration</h2>
                        <p class="card-subtitle">Known operating hours · {{ $periodLabel }}</p>
                    </div>
                    <span class="badge badge-info">Hours</span>
                </div>
                <div class="chart-box mt-3">
                    @php
                        $dailyChart = [
                            "type" => "bar",
                            "labels" => collect($daily)->pluck("label")->all(),
                            "datasets" => [[
                                "label" => "Known ON hours",
                                "data" => collect($daily)->map(fn ($row) => round($row["seconds"] / 3600, 2))->all(),
                                "color" => "#299ff2",
                                "fill" => false,
                            ]],
                            "showLegend" => false,
                            "tooltipSuffix" => "h",
                        ];
                    @endphp
                    <canvas data-chart='@json($dailyChart)'></canvas>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-divider pt-2.5 text-[10px] text-muted">
                    <span>Total {{ \App\Support\Format::duration($totals['on_seconds']) }} across {{ count($daily) }} day{{ count($daily) === 1 ? '' : 's' }}.</span>
                    <span class="inline-flex items-center gap-1"><x-icon name="info" :size="10" /> ON duration is derived from state transitions.</span>
                </div>
            </div>

            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Usage by building</h2>
                        <p class="card-subtitle">Share of known ON duration</p>
                    </div>
                </div>
                @if ($byBuilding->isEmpty())
                    <x-empty-state icon="building" title="No usage data" message="No known ON intervals fall within this period." />
                @else
                    <div class="chart-box-sm mt-2" style="height:185px">
                        @php
                            $buildingsChart = [
                                "type" => "doughnut",
                                "labels" => $byBuilding->pluck("building_name")->all(),
                                "values" => $byBuilding->pluck("on_seconds")->all(),
                                "colors" => ["#1689e8", "#20b96b", "#f2a52b", "#8798a9", "#299ff2"],
                                "cutout" => "70%",
                            ];
                        @endphp
                        <canvas data-chart='@json($buildingsChart)'></canvas>
                    </div>
                    <ul class="mt-1 space-y-1.5">
                        @foreach ($byBuilding as $index => $row)
                            <li class="flex items-center gap-2 text-[10.5px]">
                                <span class="h-2 w-2 rounded-full" style="background: {{ ['#1689e8', '#20b96b', '#f2a52b', '#8798a9', '#299ff2'][$index % 5] }}"></span>
                                <span class="flex-1 truncate text-ink-soft">{{ $row->building_name }}</span>
                                <span class="font-semibold text-ink">{{ \App\Support\Format::duration((int) $row->on_seconds, true) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Location breakdown</h2>
                    <p class="card-subtitle">Building · floor · space, sorted by known ON duration</p>
                </div>
                <span class="badge badge-info">{{ $bySpace->count() }} spaces</span>
            </div>
            @if ($bySpace->isEmpty())
                <x-empty-state icon="locations" title="No location usage yet" message="Aggregates appear once devices report known state changes." />
            @else
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Location</th><th>Type</th><th>ACs</th><th>Known ON</th><th class="w-36">Share</th></tr></thead>
                        <tbody>
                            @foreach ($bySpace as $index => $row)
                                <tr>
                                    <td>
                                        <span class="block font-semibold text-ink">{{ $row->space_name }}</span>
                                        <span class="block text-[9.5px] text-muted">{{ $row->building_name }} · {{ $row->floor_name }}</span>
                                    </td>
                                    <td class="capitalize">{{ $row->space_type }}</td>
                                    <td>{{ $row->ac_count }}</td>
                                    <td class="font-semibold text-ink">{{ \App\Support\Format::duration((int) $row->on_seconds) }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-[#edf3f8]">
                                                <div class="h-full rounded-full bg-blue-500" style="width: {{ min(100, ((int) $row->on_seconds / $totalOn) * 100) }}%"></div>
                                            </div>
                                            <span class="w-8 text-right text-[9.5px] text-muted">{{ \App\Support\Format::percent((int) $row->on_seconds, $totalOn) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @elseif ($tab === 'setpoint')
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.3fr_0.7fr]">
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Setpoint trend</h2>
                        <p class="card-subtitle">Reported target temperatures · {{ $periodLabel }}</p>
                    </div>
                    <span class="badge badge-info">°C</span>
                </div>
                @if (count($setpointSeries) === 0)
                    <x-empty-state icon="thermometer" title="No setpoint history" message="Setpoint changes reported by devices will appear here." />
                @else
                    <div class="chart-box mt-3">
                        @php
                            $setpointChart = [
                                "type" => "line",
                                "labels" => $setpointLabels,
                                "datasets" => [[
                                    "label" => "Setpoint",
                                    "data" => collect($setpointSeries)->pluck("value")->all(),
                                    "color" => "#1689e8",
                                    "fillColor" => "rgba(41,159,242,0.10)",
                                    "fill" => true,
                                ]],
                                "showLegend" => false,
                                "tooltipSuffix" => "°C",
                                "options" => ["scales" => ["y" => ["beginAtZero" => false]]],
                            ];
                        @endphp
                        <canvas data-chart='@json($setpointChart)'></canvas>
                    </div>
                    <p class="mt-2 text-[9.5px] leading-relaxed text-muted">Setpoint is the AC’s target temperature, not measured room temperature. Gaps indicate missing reports, not 0°C.</p>
                @endif
            </div>

            <div class="space-y-4">
                <div class="card card-pad">
                    <h2 class="card-title">Average setpoint</h2>
                    <p class="mt-2 text-[38px] font-bold leading-none tracking-[-0.04em] text-ink">{{ $averageSetpoint !== null ? $averageSetpoint.'°C' : 'Unknown' }}</p>
                    <p class="mt-1 text-[10.5px] text-muted">Mean of {{ count($setpointSeries) }} valid reported value{{ count($setpointSeries) === 1 ? '' : 's' }}</p>
                    <div class="mt-3 rounded-lg border border-line bg-[#fbfdff] px-3 py-2 text-[10px] leading-relaxed text-ink-soft">
                        Values outside the plausible 10–35°C band are excluded from the average. Unknown values are not replaced with zero.
                    </div>
                </div>
                <div class="card card-pad">
                    <h2 class="card-title">Setpoint data quality</h2>
                    <dl class="mt-2 space-y-2 text-[11px]">
                        <div class="flex justify-between gap-2"><dt class="text-muted">Samples</dt><dd class="font-semibold text-ink">{{ count($setpointSeries) }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Average</dt><dd class="font-semibold text-ink">{{ $averageSetpoint !== null ? $averageSetpoint.'°C' : 'Unknown' }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-muted">Range</dt><dd class="font-semibold text-ink">{{ $periodLabel }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    @elseif ($tab === 'location')
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">By building</h2>
                        <p class="card-subtitle">Known ON duration and reporting ACs</p>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Building</th><th>Site</th><th>ACs</th><th>Known ON</th></tr></thead>
                        <tbody>
                            @forelse ($byBuilding as $row)
                                <tr>
                                    <td class="font-semibold text-ink">{{ $row->building_name }}</td>
                                    <td>{{ $row->site_name }}</td>
                                    <td>{{ $row->ac_count }}</td>
                                    <td class="font-semibold text-ink">{{ \App\Support\Format::duration((int) $row->on_seconds) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No building data in this range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">By floor</h2>
                        <p class="card-subtitle">Building and floor breakdown</p>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>Floor</th><th>Building</th><th>ACs</th><th>Known ON</th></tr></thead>
                        <tbody>
                            @forelse ($byFloor as $row)
                                <tr>
                                    <td class="font-semibold text-ink">{{ $row->floor_name }}</td>
                                    <td>{{ $row->building_name }}</td>
                                    <td>{{ $row->ac_count }}</td>
                                    <td class="font-semibold text-ink">{{ \App\Support\Format::duration((int) $row->on_seconds) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No floor data in this range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">By space</h2>
                    <p class="card-subtitle">Space type and AC count</p>
                </div>
            </div>
            <div class="table-scroll">
                <table class="table">
                    <thead><tr><th>Space</th><th>Floor / building</th><th>Type</th><th>ACs</th><th>Known ON</th><th class="w-36">Share</th></tr></thead>
                    <tbody>
                        @forelse ($bySpace as $row)
                            <tr>
                                <td class="font-semibold text-ink">{{ $row->space_name }}</td>
                                <td>{{ $row->floor_name }} · {{ $row->building_name }}</td>
                                <td class="capitalize">{{ $row->space_type }}</td>
                                <td>{{ $row->ac_count }}</td>
                                <td>{{ \App\Support\Format::duration((int) $row->on_seconds) }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-[#edf3f8]"><div class="h-full rounded-full bg-blue-500" style="width: {{ min(100, ((int) $row->on_seconds / $totalOn) * 100) }}%"></div></div>
                                        <span class="w-8 text-right text-[9.5px] text-muted">{{ \App\Support\Format::percent((int) $row->on_seconds, $totalOn) }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No space data in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="mt-4 grid gap-4 xl:grid-cols-[1.4fr_0.6fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Usage by air conditioner</h2>
                        <p class="card-subtitle">Top {{ $byAc->count() }} ACs by known ON duration</p>
                    </div>
                    <a href="{{ route('acs.index') }}" class="btn btn-ghost btn-sm">Inventory</a>
                </div>
                <div class="table-scroll">
                    <table class="table">
                        <thead><tr><th>AC</th><th>Location</th><th>Known ON</th><th>Unknown</th><th>Notes</th></tr></thead>
                        <tbody>
                            @forelse ($byAc as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('acs.show', $row->id) }}" class="font-semibold text-ink hover:text-blue-700">{{ $row->asset_code }}</a>
                                        <span class="block text-[9.5px] text-muted">{{ $row->name }}</span>
                                    </td>
                                    <td>
                                        <span class="block max-w-[190px] truncate">{{ $row->space_name }}</span>
                                        <span class="block text-[9.5px] text-muted">{{ $row->building_name }} · {{ $row->site_name }}</span>
                                    </td>
                                    <td class="font-semibold text-ink">{{ \App\Support\Format::duration((int) $row->on_seconds) }}</td>
                                    <td class="text-muted">{{ \App\Support\Format::duration((int) $row->unknown_seconds) }}</td>
                                    <td>
                                        @if ((int) $row->incomplete_rows > 0)
                                            <span class="badge badge-warning">{{ $row->incomplete_rows }} incomplete</span>
                                        @else
                                            <span class="badge badge-online">Reported</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No AC usage data in this range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-4">
                <div class="card card-pad">
                    <h2 class="card-title">Most used location</h2>
                    @if ($mostUsed)
                        <p class="mt-2 text-[17px] font-bold text-ink">{{ $mostUsed->space_name }}</p>
                        <p class="text-[10.5px] text-muted">{{ $mostUsed->floor_name }} · {{ $mostUsed->building_name }}</p>
                        <p class="mt-2 text-[24px] font-bold tracking-[-0.03em] text-blue-600">{{ \App\Support\Format::duration((int) $mostUsed->on_seconds, true) }}</p>
                        <p class="text-[10px] text-muted">known ON duration</p>
                    @else
                        <p class="mt-2 text-[11px] text-muted">No location usage in this range.</p>
                    @endif
                </div>

                <div class="card card-pad">
                    <h2 class="card-title">Data caveats</h2>
                    <ul class="mt-2 space-y-1.5 text-[10px] leading-relaxed text-ink-soft">
                        <li>• ON duration is not electricity consumption.</li>
                        <li>• Unknown/stale gaps are excluded, not counted as OFF.</li>
                        <li>• Incomplete rows indicate provisional intervals.</li>
                        <li>• Physical remote actions may leave brief reporting gaps.</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <p class="mt-3 text-[9.5px] leading-relaxed text-muted">
        Analytics use aggregated known ON intervals. The system does not convert operating time to energy (kWh) because no power measurement is collected.
    </p>
@endsection
