@extends('layouts.app')

@section('page-title', 'Locations')
@section('page-subtitle', 'Browse and manage your organization’s site hierarchy')

@section('content')
    <div x-data="{ addOpen: false, addType: 'site', editOpen: false }">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <form method="GET" action="{{ route('locations.index') }}" class="flex flex-wrap items-center gap-2">
                @if ($selectedType && $selectedId)
                    <input type="hidden" name="type" value="{{ $selectedType }}">
                    <input type="hidden" name="id" value="{{ $selectedId }}">
                @endif
                <div class="relative">
                    <x-icon name="search" :size="14" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
                    <input type="search" name="q" value="{{ $q }}" placeholder="Search locations…" class="input h-[34px] w-[230px] pl-8 text-[11.5px]">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                @if ($q !== '')
                    <a href="{{ route('locations.index') }}" class="btn btn-ghost btn-sm">Clear</a>
                @endif
            </form>

            <div class="flex items-center gap-2">
                <a href="{{ route('acs.index') }}" class="btn btn-secondary btn-sm">
                    <x-icon name="ac" :size="14" /> Browse ACs
                </a>
                <button type="button" class="btn btn-primary btn-sm" @click="addOpen = true">
                    <x-icon name="plus" :size="14" /> Add location
                </button>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-[310px_minmax(0,1fr)]">
            <aside class="card self-start overflow-hidden">
                <div class="card-header py-3">
                    <div>
                        <h2 class="card-title">Hierarchy</h2>
                        <p class="card-subtitle">{{ $sites->count() }} site{{ $sites->count() === 1 ? '' : 's' }} in scope</p>
                    </div>
                    <span class="badge badge-info">Tree</span>
                </div>

                <div class="max-h-[calc(100vh-245px)] overflow-y-auto px-2.5 py-3">
                    @forelse ($sites as $site)
                        <details open class="group/site mb-1">
                            <summary class="flex cursor-pointer list-none items-center gap-1.5 rounded-lg px-2 py-1.5 text-[12px] font-semibold transition hover:bg-blue-50 [&::-webkit-details-marker]:hidden {{ $selectedType === 'site' && $selectedId === $site->id ? 'bg-blue-100 text-blue-700' : 'text-ink' }}">
                                <x-icon name="chevron-right" :size="11" class="text-muted transition group-open/site:rotate-90" />
                                <x-icon name="locations" :size="13" class="text-blue-600" />
                                <a href="{{ route('locations.index', ['type' => 'site', 'id' => $site->id]) }}" class="min-w-0 flex-1 truncate hover:underline">{{ $site->name }}</a>
                                <span class="text-[9.5px] font-medium text-muted">{{ $site->buildings->count() }}</span>
                            </summary>

                            <div class="ml-4 border-l border-divider pl-1.5">
                                @forelse ($site->buildings as $building)
                                    <details open class="group/building">
                                        <summary class="flex cursor-pointer list-none items-center gap-1.5 rounded-md px-1.5 py-1 text-[11.5px] transition hover:bg-blue-50 [&::-webkit-details-marker]:hidden {{ $selectedType === 'building' && $selectedId === $building->id ? 'bg-blue-100 font-semibold text-blue-700' : 'text-ink-soft' }}">
                                            <x-icon name="chevron-right" :size="10" class="text-muted transition group-open/building:rotate-90" />
                                            <x-icon name="building" :size="12" class="text-[#5f8fb8]" />
                                            <a href="{{ route('locations.index', ['type' => 'building', 'id' => $building->id]) }}" class="min-w-0 flex-1 truncate hover:underline">{{ $building->name }}</a>
                                        </summary>

                                        <div class="ml-4 border-l border-divider pl-1.5">
                                            @forelse ($building->floors as $floor)
                                                <details open class="group/floor">
                                                    <summary class="flex cursor-pointer list-none items-center gap-1.5 rounded-md px-1.5 py-1 text-[11px] transition hover:bg-blue-50 [&::-webkit-details-marker]:hidden {{ $selectedType === 'floor' && $selectedId === $floor->id ? 'bg-blue-100 font-semibold text-blue-700' : 'text-ink-soft' }}">
                                                        <x-icon name="chevron-right" :size="9" class="text-muted transition group-open/floor:rotate-90" />
                                                        <x-icon name="floor" :size="11" class="text-[#7ba3c4]" />
                                                        <a href="{{ route('locations.index', ['type' => 'floor', 'id' => $floor->id]) }}" class="min-w-0 flex-1 truncate hover:underline">{{ $floor->name }}</a>
                                                    </summary>
                                                    <div class="ml-4 border-l border-divider pl-1">
                                                        @include('locations.partials.space-nodes', ['spaces' => $floor->spaces, 'selectedType' => $selectedType, 'selectedId' => $selectedId])
                                                    </div>
                                                </details>
                                            @empty
                                                <p class="px-2 py-1 text-[10px] italic text-muted">No floors</p>
                                            @endforelse
                                        </div>
                                    </details>
                                @empty
                                    <p class="px-2 py-1 text-[10px] italic text-muted">No buildings</p>
                                @endforelse
                            </div>
                        </details>
                    @empty
                        <x-empty-state icon="locations" title="No locations found" message="{{ $q ? 'No site, building, floor or space matches your search.' : 'Add your first site to begin.' }}" />
                    @endforelse
                </div>
            </aside>

            <div class="min-w-0 space-y-4">
                @if (! $selected)
                    <div class="card">
                        <x-empty-state icon="locations" title="Select a location" message="Choose a site, building, floor or space from the hierarchy.">
                            <button type="button" class="btn btn-primary btn-sm" @click="addOpen = true"><x-icon name="plus" :size="13" /> Add location</button>
                        </x-empty-state>
                    </div>
                @else
                    <div class="card overflow-hidden">
                        <div class="card-pad">
                            <nav class="flex flex-wrap items-center gap-1 text-[10.5px] text-muted" aria-label="Breadcrumb">
                                @foreach ($breadcrumb as $crumb)
                                    <a href="{{ route('locations.index', ['type' => $crumb['type'], 'id' => $crumb['id']]) }}" class="hover:text-blue-700 hover:underline">{{ $crumb['label'] }}</a>
                                    <x-icon name="chevron-right" :size="10" />
                                @endforeach
                                <span class="font-semibold text-ink-soft">{{ $selected->name }}</span>
                            </nav>

                            <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h1 class="truncate text-[20px] font-bold tracking-[-0.025em] text-ink">{{ $selected->name }}</h1>
                                        <span class="badge badge-info">{{ ucfirst($selectedType) }}</span>
                                        @if (isset($selected->status))
                                            <span class="badge {{ $selected->status === 'active' ? 'badge-online' : 'badge-unknown' }}">{{ ucfirst($selected->status) }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-[11.5px] text-ink-soft">
                                        {{ collect($breadcrumb)->pluck('label')->implode(' · ') }}
                                        @if ($selectedType === 'space') · {{ $selected->typeLabel() }} @endif
                                        @if ($selectedType === 'building' && $selected->site) · {{ $selected->site->name }} @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="btn btn-secondary btn-sm" @click="editOpen = true"><x-icon name="edit" :size="13" /> Edit</button>
                                    <form method="POST" action="{{ route('locations.destroy', ['locationType' => $selectedType, 'id' => $selectedId]) }}" onsubmit="return confirm('Delete this {{ $selectedType }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm text-danger hover:bg-danger-soft"><x-icon name="trash" :size="13" /> Delete</button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Total ACs</p>
                                    <p class="mt-1 text-[20px] font-bold leading-none text-ink">{{ $summary['total'] }}</p>
                                </div>
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Known ON</p>
                                    <p class="mt-1 flex items-center gap-1.5 text-[20px] font-bold leading-none text-[#0d8c4e]"><span class="mini-dot bg-success"></span>{{ $summary['on'] }}</p>
                                </div>
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Known OFF</p>
                                    <p class="mt-1 flex items-center gap-1.5 text-[20px] font-bold leading-none text-ink-soft"><span class="mini-dot bg-neutral"></span>{{ $summary['off'] }}</p>
                                </div>
                                <div class="rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                    <p class="text-[10px] font-medium uppercase tracking-[0.05em] text-muted">Today’s ON duration</p>
                                    <p class="mt-1 text-[20px] font-bold leading-none text-ink">{{ \App\Support\Format::duration($summary['today_seconds'], true) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 2xl:grid-cols-[minmax(0,1fr)_300px]">
                        <div class="card overflow-hidden">
                            <div class="card-header">
                                <div>
                                    <h2 class="card-title">Spaces</h2>
                                    <p class="card-subtitle">{{ $spaces->count() }} space{{ $spaces->count() === 1 ? '' : 's' }} directly in this location</p>
                                </div>
                                <a href="{{ route('acs.index', ['space_id' => $selectedType === 'space' ? $selectedId : null, 'floor_id' => $selectedType === 'floor' ? $selectedId : null, 'building_id' => $selectedType === 'building' ? $selectedId : null, 'site_id' => $selectedType === 'site' ? $selectedId : null]) }}" class="btn btn-ghost btn-sm">View ACs <x-icon name="arrow-right" :size="12" /></a>
                            </div>

                            @if ($spaces->isNotEmpty())
                                <div class="grid grid-cols-2 gap-2.5 p-3 sm:grid-cols-3 2xl:grid-cols-4">
                                    @foreach ($spaces as $space)
                                        @php $spaceModel = $space['model']; @endphp
                                        <a href="{{ route('locations.index', ['type' => 'space', 'id' => $spaceModel->id]) }}" class="space-card {{ $selectedType === 'space' && $selectedId === $spaceModel->id ? 'space-card-active' : '' }}">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0">
                                                    <p class="truncate text-[12px] font-semibold text-ink">{{ $spaceModel->name }}</p>
                                                    <p class="text-[9.5px] text-muted">{{ $spaceModel->typeLabel() }}@if ($spaceModel->floor) · {{ $spaceModel->floor->name }}@endif</p>
                                                </div>
                                                <x-icon name="space" :size="13" class="text-[#8fb6d8]" />
                                            </div>
                                            <div class="mt-2 flex items-center justify-between">
                                                <span class="text-[10px] text-ink-soft"><strong class="text-[13px] font-bold text-ink">{{ $space['acs_count'] }}</strong> ACs</span>
                                                <span class="text-[10px] text-muted">{{ \App\Support\Format::duration($space['today_seconds'], true) }}</span>
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center gap-1">
                                                @if ($space['on'] > 0)<span class="badge badge-on">{{ $space['on'] }} ON</span>@endif
                                                @if ($space['off'] > 0)<span class="badge badge-off">{{ $space['off'] }} OFF</span>@endif
                                                @if ($space['unknown'] > 0)<span class="badge badge-unknown">{{ $space['unknown'] }} unknown</span>@endif
                                                @if ($space['acs_count'] === 0)<span class="badge badge-unknown">No ACs</span>@endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @elseif ($acs->isNotEmpty())
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($acs as $ac)
                                        @php
                                            $snapshot = $ac->stateSnapshot;
                                            $stale = $snapshot?->isStale() ?? true;
                                            $setpoint = $snapshot?->temperature_setpoint_c;
                                        @endphp
                                        <a href="{{ route('acs.control', $ac) }}" class="card overflow-hidden transition hover:border-blue-300 hover:shadow-[0_10px_24px_-18px_rgba(8,117,201,0.4)] block">
                                            <div class="p-3">
                                                <div class="flex items-start justify-between gap-2 mb-2">
                                                    <div class="min-w-0">
                                                        <span class="block truncate text-[12px] font-bold text-ink">{{ $ac->asset_code }}</span>
                                                        <p class="block text-[10px] text-muted truncate">{{ $ac->name }}</p>
                                                    </div>
                                                    <x-status-badge :state="$snapshot?->power_state" :stale="$stale" :quality="$snapshot?->quality?->value" />
                                                </div>

                                                <div class="mt-1.5 flex items-center gap-1.5 text-[9.5px] text-ink-soft">
                                                    <x-icon name="space" :size="10" class="text-[#8fb6d8]" />
                                                    <span class="truncate">{{ $ac->space?->name ?? 'Unassigned' }}</span>
                                                    @if ($ac->space?->floor)
                                                        <span class="text-muted">· {{ $ac->space->floor->name }}</span>
                                                    @endif
                                                </div>

                                                <div class="mt-2 flex items-center justify-between">
                                                    <div class="text-left">
                                                        <p class="text-[9px] uppercase tracking-[0.05em] text-muted">Setpoint</p>
                                                        <p class="mt-0.5 text-[14px] font-bold text-ink">{{ $setpoint !== null ? $setpoint.'°C' : '—' }}</p>
                                                    </div>
                                                    <div class="text-left">
                                                        <p class="text-[9px] uppercase tracking-[0.05em] text-muted">Connectivity</p>
                                                        <x-connectivity-badge :status="$ac->connectivity()" />
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <x-empty-state icon="ac" title="No Air Conditioners Installed" message="There are currently no air conditioners assigned to this location." />
                            @endif
                        </div>

                        <aside class="space-y-4">
                            <div class="card card-pad">
                                <div class="flex items-start gap-2">
                                    <span class="icon-tile bg-blue-50 text-blue-600"><x-icon name="shield" :size="16" /></span>
                                    <div>
                                        <h2 class="text-[12.5px] font-semibold text-ink">Physical remote policy</h2>
                                        <p class="text-[10px] text-muted">Nearest explicit setting wins</p>
                                    </div>
                                </div>
                                @if ($policy)
                                    <div class="mt-3 rounded-lg border border-line bg-[#fbfdff] px-3 py-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] uppercase tracking-[0.05em] text-muted">Effective</span>
                                            <span class="badge {{ $policy['value']->value === 'enabled' ? 'badge-online' : 'badge-off' }}">{{ $policy['value']->label() }}</span>
                                        </div>
                                        <p class="mt-1.5 text-[10.5px] text-ink-soft">Source: <span class="font-semibold text-ink">{{ $policy['source_label'] }}</span></p>
                                        <p class="text-[9.5px] text-muted">Version {{ $policy['version'] }}</p>
                                    </div>

                                    <form method="POST" action="{{ route('settings.policy.update') }}" class="mt-3 space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="scope_type" value="{{ $policyScope }}">
                                        <input type="hidden" name="scope_id" value="{{ $policyScopeId }}">
                                        <label class="label mb-0" for="location-policy">Set for this {{ $selectedType }}</label>
                                        <select id="location-policy" name="policy_value" class="select text-[11.5px]">
                                            <option value="inherit">Inherit from parent</option>
                                            <option value="enabled">Enabled</option>
                                            <option value="disabled">Disabled</option>
                                        </select>
                                        <button type="submit" class="btn btn-secondary btn-sm w-full">Apply policy</button>
                                    </form>
                                @endif
                                <p class="mt-3 text-[9.5px] leading-relaxed text-muted">Software cannot physically block a handheld remote’s direct IR beam. Policy controls synchronization and reporting only.</p>
                            </div>

                            <div class="card card-pad">
                                <h2 class="text-[12.5px] font-semibold text-ink">Location details</h2>
                                <dl class="mt-2.5 space-y-2 text-[11px]">
                                    <div class="flex justify-between gap-2"><dt class="text-muted">Type</dt><dd class="font-medium text-ink">{{ ucfirst($selectedType) }}</dd></div>
                                    @if (isset($selected->code) && $selected->code)
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Code</dt><dd class="font-medium text-ink">{{ $selected->code }}</dd></div>
                                    @endif
                                    @if ($selectedType === 'site')
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Timezone</dt><dd class="font-medium text-ink">{{ $selected->timezone ?: $organization->timezone }}</dd></div>
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Address</dt><dd class="max-w-[150px] text-right font-medium text-ink">{{ $selected->address ?: '—' }}</dd></div>
                                    @endif
                                    @if ($selectedType === 'space')
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Parent</dt><dd class="font-medium text-ink">{{ $selected->parent?->name ?? 'Floor level' }}</dd></div>
                                        <div class="flex justify-between gap-2"><dt class="text-muted">Area</dt><dd class="font-medium text-ink">{{ $selected->area_sqm ? $selected->area_sqm.' m²' : '—' }}</dd></div>
                                    @endif
                                    <div class="flex justify-between gap-2"><dt class="text-muted">ACs</dt><dd class="font-medium text-ink">{{ $acs->count() }}</dd></div>
                                </dl>
                            </div>
                        </aside>
                    </div>

                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <div>
                                <h2 class="card-title">Air conditioners in this location</h2>
                                <p class="card-subtitle">Includes nested spaces</p>
                            </div>
                            <a href="{{ route('acs.index') }}" class="btn btn-ghost btn-sm">Full inventory</a>
                        </div>
                        @if ($acs->isEmpty())
                            <x-empty-state icon="ac" title="No air conditioners yet" message="Register an AC and assign it to a space in this location." />
                        @else
                            <div class="table-scroll">
                                <table class="table">
                                    <thead><tr><th>AC</th><th>Space</th><th>Model</th><th>State</th><th>Setpoint</th><th>Device</th><th></th></tr></thead>
                                    <tbody>
                                        @foreach ($acs->take(12) as $ac)
                                            <tr>
                                                <td><a href="{{ route('acs.show', $ac) }}" class="font-semibold text-ink hover:text-blue-700">{{ $ac->asset_code }}</a><span class="block text-[10px] text-muted">{{ $ac->name }}</span></td>
                                                <td>{{ $ac->space?->name ?? '—' }}</td>
                                                <td>{{ $ac->acModel?->displayName() ?? '—' }}</td>
                                                <td><x-status-badge :state="$ac->stateSnapshot?->power_state" :stale="$ac->stateSnapshot?->isStale() ?? true" /></td>
                                                <td>{{ $ac->stateSnapshot?->temperature_setpoint_c !== null ? $ac->stateSnapshot->temperature_setpoint_c.'°C' : 'Unknown' }}</td>
                                                <td><x-connectivity-badge :status="$ac->connectivity()" /></td>
                                                <td class="text-right"><a href="{{ route('acs.control', $ac) }}" class="btn btn-soft btn-sm">Control</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Add location modal --}}
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-navy-950/45 p-4 backdrop-blur-sm" x-show="addOpen" x-cloak x-transition.opacity>
            <div class="mt-8 w-full max-w-lg rounded-2xl border border-line bg-white shadow-2xl" @click.outside="addOpen = false" x-transition>
                <div class="flex items-center justify-between border-b border-divider px-5 py-3.5">
                    <div>
                        <h2 class="text-[15px] font-semibold text-ink">Add location</h2>
                        <p class="text-[10.5px] text-muted">Spaces can be nested under other spaces.</p>
                    </div>
                    <button type="button" class="btn btn-icon btn-ghost" @click="addOpen = false"><x-icon name="close" :size="16" /></button>
                </div>
                <form method="POST" action="{{ route('locations.store') }}" class="space-y-3 px-5 py-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="add-location-type">Location type</label>
                            <select id="add-location-type" name="location_type" class="select" x-model="addType" required>
                                <option value="site">Site</option>
                                <option value="building">Building</option>
                                <option value="floor">Floor</option>
                                <option value="space">Space</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="add-location-name">Name</label>
                            <input id="add-location-name" name="name" class="input" required maxlength="120" placeholder="e.g. Floor 2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label" for="add-location-code">Code <span class="font-normal normal-case tracking-normal text-muted">(optional)</span></label>
                            <input id="add-location-code" name="code" class="input" maxlength="40" placeholder="e.g. HO-F2">
                        </div>
                        <div x-show="addType === 'floor'">
                            <label class="label" for="add-floor-number">Floor number</label>
                            <input id="add-floor-number" name="floor_number" type="number" class="input" min="-10" max="200">
                        </div>
                    </div>

                    <div x-show="addType === 'building'">
                        <label class="label" for="add-site-id">Parent site</label>
                        <select id="add-site-id" name="site_id" class="select" x-bind:disabled="addType !== 'building'">
                            @foreach ($allSites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="addType === 'floor'">
                        <label class="label" for="add-building-id">Parent building</label>
                        <select id="add-building-id" name="building_id" class="select" x-bind:disabled="addType !== 'floor'">
                            @foreach ($allBuildings as $building)
                                <option value="{{ $building->id }}">{{ $building->site?->name }} · {{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="addType === 'space'">
                        <div>
                            <label class="label" for="add-floor-id">Parent floor</label>
                            <select id="add-floor-id" name="floor_id" class="select" x-bind:disabled="addType !== 'space'">
                                @foreach ($allFloors as $floor)
                                    <option value="{{ $floor->id }}">{{ $floor->building?->name }} · {{ $floor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label" for="add-space-type">Space type</label>
                            <select id="add-space-type" name="space_type" class="select" x-bind:disabled="addType !== 'space'">
                                @foreach (\App\Models\Space::TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div x-show="addType === 'space'">
                        <label class="label" for="add-parent-space">Parent space <span class="font-normal normal-case tracking-normal text-muted">(optional, must be on the same floor)</span></label>
                        <select id="add-parent-space" name="parent_space_id" class="select" x-bind:disabled="addType !== 'space'">
                            <option value="">None — top-level space on floor</option>
                            @foreach ($allSpaces as $space)
                                <option value="{{ $space->id }}">{{ $space->floor?->building?->name }} · {{ $space->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="addType === 'space'">
                        <div>
                            <label class="label" for="add-area">Area (m²)</label>
                            <input id="add-area" name="area_sqm" type="number" step="0.01" min="0" class="input" x-bind:disabled="addType !== 'space'">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="addType === 'site'">
                        <div>
                            <label class="label" for="add-timezone">Site timezone</label>
                            <input id="add-timezone" name="timezone" class="input" :value="'{{ $organization->timezone }}'" x-bind:disabled="addType !== 'site'" placeholder="e.g. Asia/Karachi">
                        </div>
                        <div>
                            <label class="label" for="add-address">Address</label>
                            <input id="add-address" name="address" class="input" x-bind:disabled="addType !== 'site'">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-divider pt-3">
                        <button type="button" class="btn btn-secondary" @click="addOpen = false">Cancel</button>
                        <button type="submit" class="btn btn-primary"><x-icon name="plus" :size="14" /> Create location</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit selected location --}}
        @if ($selected)
            <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-navy-950/45 p-4 backdrop-blur-sm" x-show="editOpen" x-cloak x-transition.opacity>
                <div class="mt-8 w-full max-w-lg rounded-2xl border border-line bg-white shadow-2xl" @click.outside="editOpen = false" x-transition>
                    <div class="flex items-center justify-between border-b border-divider px-5 py-3.5">
                        <div>
                            <h2 class="text-[15px] font-semibold text-ink">Edit {{ $selectedType }}</h2>
                            <p class="text-[10.5px] text-muted">{{ $selected->name }}</p>
                        </div>
                        <button type="button" class="btn btn-icon btn-ghost" @click="editOpen = false"><x-icon name="close" :size="16" /></button>
                    </div>
                    <form method="POST" action="{{ route('locations.update', ['locationType' => $selectedType, 'id' => $selectedId]) }}" class="space-y-3 px-5 py-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label" for="edit-name">Name</label>
                                <input id="edit-name" name="name" value="{{ $selected->name }}" class="input" required maxlength="120">
                            </div>
                            <div>
                                <label class="label" for="edit-code">Code</label>
                                <input id="edit-code" name="code" value="{{ $selected->code }}" class="input" maxlength="40">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label" for="edit-status">Status</label>
                                <select id="edit-status" name="status" class="select">
                                    <option value="active" @selected($selected->status === 'active')>Active</option>
                                    <option value="inactive" @selected($selected->status === 'inactive')>Inactive</option>
                                </select>
                            </div>
                            @if ($selectedType === 'floor')
                                <div>
                                    <label class="label" for="edit-floor-number">Floor number</label>
                                    <input id="edit-floor-number" name="floor_number" type="number" value="{{ $selected->floor_number }}" class="input" min="-10" max="200">
                                </div>
                            @endif
                            @if ($selectedType === 'space')
                                <div>
                                    <label class="label" for="edit-space-type">Space type</label>
                                    <select id="edit-space-type" name="space_type" class="select">
                                        @foreach (\App\Models\Space::TYPES as $value => $label)
                                            <option value="{{ $value }}" @selected($selected->space_type === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        @if ($selectedType === 'space')
                            <div>
                                <label class="label" for="edit-area">Area (m²)</label>
                                <input id="edit-area" name="area_sqm" type="number" step="0.01" value="{{ $selected->area_sqm }}" class="input">
                            </div>
                        @endif
                        @if ($selectedType === 'site')
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label" for="edit-timezone">Timezone</label>
                                    <input id="edit-timezone" name="timezone" value="{{ $selected->timezone }}" class="input">
                                </div>
                                <div>
                                    <label class="label" for="edit-address">Address</label>
                                    <input id="edit-address" name="address" value="{{ $selected->address }}" class="input">
                                </div>
                            </div>
                        @endif
                        <div class="flex justify-end gap-2 border-t border-divider pt-3">
                            <button type="button" class="btn btn-secondary" @click="editOpen = false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
