@extends('layouts.app')

@section('page-title', 'Activity History')
@section('page-subtitle', 'Chronological record of portal, physical remote, device and system events')

@section('content')
    @php
        $sourceIcon = fn ($source) => match ($source?->value) {
            'physical_remote' => 'wifi',
            'device' => 'device',
            'system' => 'settings',
            default => 'user',
        };
        $resultClass = fn ($result) => match ($result) {
            'failure' => 'badge-danger',
            'pending' => 'badge-warning',
            'warning' => 'badge-warning',
            'success' => 'badge-online',
            default => 'badge-info',
        };
    @endphp

    <form method="GET" action="{{ route('activity.index') }}" class="card mb-4 p-3.5">
        <div class="flex flex-wrap items-end gap-2">
            <div class="relative min-w-[220px] flex-1">
                <label class="label" for="activity-search">Search</label>
                <x-icon name="search" :size="14" class="pointer-events-none absolute bottom-[10px] left-3 text-muted" />
                <input id="activity-search" type="search" name="q" value="{{ $filters['q'] }}" class="input h-[34px] pl-8 text-[12px]" placeholder="Search action, description, AC name or asset ID…">
            </div>
            <div>
                <label class="label" for="activity-source">Source</label>
                <select id="activity-source" name="source" class="select h-[34px] w-[145px] py-0 text-[11.5px]">
                    <option value="">All sources</option>
                    @foreach ($sources as $source)
                        <option value="{{ $source->value }}" @selected($filters['source'] === $source->value)>{{ $source->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="activity-result">Result</label>
                <select id="activity-result" name="result" class="select h-[34px] w-[120px] py-0 text-[11.5px]">
                    <option value="">All results</option>
                    @foreach ($results as $result)
                        <option value="{{ $result }}" @selected($filters['result'] === $result)>{{ ucfirst($result) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="activity-site">Site</label>
                <select id="activity-site" name="site_id" class="select h-[34px] w-[130px] py-0 text-[11.5px]">
                    <option value="">All sites</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" @selected($filters['site_id'] === $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="activity-ac">AC</label>
                <select id="activity-ac" name="ac_id" class="select h-[34px] w-[140px] py-0 text-[11.5px]">
                    <option value="">All ACs</option>
                    @foreach ($acs as $ac)
                        <option value="{{ $ac->id }}" @selected($filters['ac_id'] === $ac->id)>{{ $ac->asset_code }} · {{ $ac->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="activity-from">From</label>
                <input id="activity-from" type="date" name="from" value="{{ $filters['from'] }}" class="input h-[34px] w-[135px] py-0 text-[11.5px]">
            </div>
            <div>
                <label class="label" for="activity-to">To</label>
                <input id="activity-to" type="date" name="to" value="{{ $filters['to'] }}" class="input h-[34px] w-[135px] py-0 text-[11.5px]">
            </div>
            <button type="submit" class="btn btn-primary btn-sm h-[34px]"><x-icon name="filter" :size="13" /> Apply</button>
            <a href="{{ route('activity.index') }}" class="btn btn-ghost btn-sm h-[34px]">Reset</a>
        </div>
    </form>

    <div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-muted">
        <p>
            <span class="font-semibold text-ink">{{ number_format($activity->total()) }}</span> event{{ $activity->total() === 1 ? '' : 's' }}
            @if ($filters['q'] !== '') · matching “{{ $filters['q'] }}” @endif
            @if ($filters['source'] !== '') · {{ ucfirst(str_replace('_', ' ', $filters['source'])) }} @endif
            @if ($filters['result'] !== '') · {{ $filters['result'] }} @endif
        </p>
        <p class="inline-flex items-center gap-1"><x-icon name="clock" :size="12" /> Times shown in {{ $timezone }}</p>
    </div>

    <div class="card overflow-hidden">
        @if ($activity->isEmpty())
            <x-empty-state icon="activity" title="No activity matches" message="Adjust the filters or date range to find events. Commands, remote actions and device reports are recorded automatically." />
        @else
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[145px]">Timestamp</th>
                            <th>AC</th>
                            <th>Location</th>
                            <th>Action</th>
                            <th>Source</th>
                            <th>Result</th>
                            <th>Actor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activity as $log)
                            <tr>
                                <td>
                                    <span class="block font-medium text-ink">{{ \App\Support\Format::datetime($log->occurred_at, $timezone) }}</span>
                                    <span class="block text-[9.5px] text-muted">{{ $log->occurred_at?->diffForHumans(short: true) }}</span>
                                </td>
                                <td>
                                    @if ($log->acUnit)
                                        <a href="{{ route('acs.show', $log->acUnit) }}" class="font-semibold text-ink hover:text-blue-700">{{ $log->acUnit->asset_code }}</a>
                                        <span class="block text-[9.5px] text-muted">{{ $log->acUnit->name }}</span>
                                    @else
                                        <span class="text-muted">Organization</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="block max-w-[210px] truncate">{{ $log->acUnit?->locationLabel() ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="block max-w-[340px] font-medium text-ink">{{ $log->description }}</span>
                                    <span class="block font-mono text-[9px] text-muted">{{ $log->action }}</span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 text-[11px]">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $log->source === \App\Enums\EventSource::PhysicalRemote ? 'bg-warning-soft text-[#b77405]' : 'bg-blue-50 text-blue-600' }}">
                                            <x-icon :name="$sourceIcon($log->source)" :size="12" />
                                        </span>
                                        {{ $log->source->label() }}
                                    </span>
                                </td>
                                <td><span class="badge {{ $resultClass($log->result) }}">{{ ucfirst($log->result ?? 'info') }}</span></td>
                                <td>
                                    <span class="block text-[11px]">{{ $log->user?->name ?? '—' }}</span>
                                    @if ($log->device)
                                        <span class="block text-[9.5px] text-muted">{{ $log->device->name }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($activity->hasPages())
        <div class="mt-4">{{ $activity->links() }}</div>
    @endif

    <p class="mt-3 text-[9.5px] leading-relaxed text-muted">
        Physical remote events are observed and reported by the ESP32. A restricted remote action means software did not synchronize it — the direct IR beam may still have reached the AC. IR transmission success is not physical confirmation.
    </p>
@endsection
