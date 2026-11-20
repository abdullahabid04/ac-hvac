@extends('layouts.app')

@section('page-title', 'Reports')
@section('page-subtitle', 'Generate and download operational reports — PDF or Excel-compatible CSV')

@section('content')
    @php
        $tz = $organization->timezone ?: 'UTC';
        $defaultFrom = now($tz)->subDays(6)->toDateString();
        $defaultTo = now($tz)->toDateString();
        $typeDescriptions = [
            'daily_usage' => 'Day-by-day known ON duration across the selected scope.',
            'weekly_usage' => 'Week-by-week operating hours for the selected period.',
            'monthly_usage' => 'Month-by-month operating hours for the selected period.',
            'location_usage' => 'Operating hours grouped by building, floor and space.',
            'ac_history' => 'Chronological ON/OFF state changes reported by devices.',
            'setpoint_history' => 'Reported target-temperature changes over time.',
            'device_connectivity' => 'Device connectivity, last seen and remote policy state.',
        ];
        $typeIcons = [
            'daily_usage' => 'calendar',
            'weekly_usage' => 'analytics',
            'monthly_usage' => 'reports',
            'location_usage' => 'locations',
            'ac_history' => 'power',
            'setpoint_history' => 'thermometer',
            'device_connectivity' => 'device',
        ];
        $statusClass = fn ($status) => match ($status) {
            'completed' => 'badge-online',
            'failed' => 'badge-danger',
            default => 'badge-warning',
        };
    @endphp

    <div x-data="{ type: 'daily_usage', format: 'pdf' }">
        <div class="mb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($reportTypes as $value => $label)
                <button type="button" @click="type = '{{ $value }}'"
                        class="nav-card h-full flex-col !items-start gap-2 text-left"
                        :class="type === '{{ $value }}' ? 'border-blue-500 ring-2 ring-blue-100' : ''">
                    <span class="flex w-full items-start justify-between gap-2">
                        <span class="icon-tile bg-blue-50 text-blue-600"><x-icon :name="$typeIcons[$value] ?? 'reports'" :size="17" /></span>
                        <span class="h-4 w-4 rounded-full border-2 transition" :class="type === '{{ $value }}' ? 'border-blue-600 bg-blue-600 ring-2 ring-white ring-inset' : 'border-[#c9d8e6]'"></span>
                    </span>
                    <span class="block text-[12px] font-semibold text-ink">{{ $label }}</span>
                    <span class="block text-[10px] leading-relaxed text-muted">{{ $typeDescriptions[$value] ?? 'Operational report.' }}</span>
                </button>
            @endforeach
        </div>

        <div class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Generate report</h2>
                        <p class="card-subtitle">Reports are generated immediately and stored for 30 days</p>
                    </div>
                    <span class="badge badge-info" x-text="type.replaceAll('_', ' ')"></span>
                </div>

                <form method="POST" action="{{ route('reports.store') }}" class="space-y-3 p-4 md:p-[18px]">
                    @csrf
                    <input type="hidden" name="report_type" :value="type">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="report-from">From</label>
                            <input id="report-from" type="date" name="from" value="{{ old('from', $defaultFrom) }}" class="input" required>
                        </div>
                        <div>
                            <label class="label" for="report-to">To</label>
                            <input id="report-to" type="date" name="to" value="{{ old('to', $defaultTo) }}" class="input" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="report-site">Site <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-site" name="site_id" class="select">
                                <option value="">All sites in organization</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label" for="report-building">Building <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-building" name="building_id" class="select">
                                <option value="">All buildings</option>
                                @foreach ($buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label" for="report-floor">Floor <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-floor" name="floor_id" class="select">
                                <option value="">All floors</option>
                                @foreach ($floors as $floor)
                                    <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label" for="report-space">Space <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <select id="report-space" name="space_id" class="select">
                                <option value="">All spaces</option>
                                @foreach ($spaces as $space)
                                    <option value="{{ $space->id }}">{{ $space->floor?->building?->name }} · {{ $space->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="label" for="report-ac">Air conditioner <span class="font-normal normal-case tracking-normal text-muted">(optional, narrows the report to one AC)</span></label>
                        <select id="report-ac" name="ac_id" class="select">
                            <option value="">All air conditioners in scope</option>
                            @foreach ($acs as $ac)
                                <option value="{{ $ac->id }}">{{ $ac->asset_code }} · {{ $ac->name }} — {{ $ac->locationLabel() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <span class="label">Format</span>
                        <div class="flex items-center gap-2">
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-line bg-white px-3 py-2 text-[11.5px] font-semibold text-ink-soft transition" :class="format === 'pdf' ? 'border-blue-500 bg-blue-50 text-blue-700' : ''">
                                <input type="radio" name="format" value="pdf" x-model="format" class="sr-only">
                                <x-icon name="reports" :size="14" /> PDF
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-line bg-white px-3 py-2 text-[11.5px] font-semibold text-ink-soft transition" :class="format === 'csv' ? 'border-blue-500 bg-blue-50 text-blue-700' : ''">
                                <input type="radio" name="format" value="csv" x-model="format" class="sr-only">
                                <x-icon name="export" :size="14" /> CSV / Excel
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 border-t border-divider pt-3">
                        <p class="max-w-[240px] text-[9.5px] leading-relaxed text-muted">Reports contain known ON duration, not energy consumption (kWh).</p>
                        <button type="submit" class="btn btn-primary"><x-icon name="download" :size="14" /> Generate report</button>
                    </div>
                </form>
            </div>

            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="card-title">Report guidance</h2>
                        <p class="card-subtitle">What the numbers mean</p>
                    </div>
                    <x-icon name="info" :size="16" class="text-muted" />
                </div>
                <ul class="mt-3 space-y-2.5 text-[10.5px] leading-relaxed text-ink-soft">
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">ON duration</strong> is operating time derived from known ON/OFF transitions. It is not electricity consumption and is never converted to kWh.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">Unknown or stale</strong> intervals are excluded from totals and never treated as OFF time.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">Setpoint</strong> is the target temperature, not measured room temperature.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">IR transmission success</strong> is not physical confirmation that the AC changed state.</span></li>
                    <li class="flex gap-2"><span class="mt-[5px] h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span><span><strong class="text-ink">Sample data</strong> in development is illustrative and is not production evidence.</span></li>
                </ul>
                <div class="mt-3 rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5 text-[10px] leading-relaxed text-muted">
                    Scope precedence for report filters is AC, then space/building/floor/site. Files are stored privately and access-checked against your organization.
                </div>
            </div>
        </div>

        <div class="mt-4 card overflow-hidden">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Generated reports</h2>
                    <p class="card-subtitle">Private downloads scoped to your organization</p>
                </div>
                <span class="badge badge-unknown">{{ number_format($reports->total()) }} stored</span>
            </div>

            @if ($reports->isEmpty())
                <x-empty-state icon="reports" title="No reports generated yet" message="Choose a report type above, set the period and scope, then generate a PDF or CSV." />
            @else
                <div class="table-scroll">
                    <table class="table">
                        <thead>
                            <tr><th>Report</th><th>Period</th><th>Scope</th><th>Format</th><th>Status</th><th>Requested by</th><th>Created</th><th class="text-right">Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                @php
                                    $filters = $report->filters ?? [];
                                    if (isset($filters['ac_id'])) {
                                        $scopeLabel = 'AC selected';
                                    } elseif (isset($filters['space_id'])) {
                                        $scopeLabel = 'Space selected';
                                    } elseif (isset($filters['building_id'])) {
                                        $scopeLabel = 'Building selected';
                                    } elseif (isset($filters['floor_id'])) {
                                        $scopeLabel = 'Floor selected';
                                    } elseif (isset($filters['site_id'])) {
                                        $scopeLabel = 'Site selected';
                                    } else {
                                        $scopeLabel = 'Organization-wide';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span class="block font-semibold text-ink">{{ $report->title }}</span>
                                        <span class="block font-mono text-[9px] text-muted">{{ str($report->id)->substr(0, 8) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap text-[10.5px]">
                                        @if (! empty($filters['from']) && ! empty($filters['to']))
                                            {{ \Illuminate\Support\Carbon::parse($filters['from'])->format('d M Y') }} – {{ \Illuminate\Support\Carbon::parse($filters['to'])->format('d M Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $scopeLabel }}</td>
                                    <td><span class="badge badge-info">{{ strtoupper($report->format) }}</span></td>
                                    <td><span class="badge {{ $statusClass($report->status) }}">{{ ucfirst($report->status) }}</span></td>
                                    <td>{{ $report->requestedBy?->name ?? 'System' }}</td>
                                    <td class="whitespace-nowrap text-[10.5px]">{{ \App\Support\Format::datetime($report->created_at, $tz) }}</td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1">
                                            @if ($report->status === 'completed')
                                                <a href="{{ route('reports.download', $report) }}" class="btn btn-soft btn-sm"><x-icon name="download" :size="12" /> Download</a>
                                            @endif
                                            <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Delete this report and its file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-sm text-danger hover:bg-danger-soft" title="Delete report"><x-icon name="trash" :size="12" /></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($reports->hasPages())
            <div class="mt-4">{{ $reports->links() }}</div>
        @endif
    </div>
@endsection
