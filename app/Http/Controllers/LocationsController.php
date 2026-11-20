<?php

namespace App\Http\Controllers;

use App\Enums\PolicyScope;
use App\Enums\PowerState;
use App\Models\AcUnit;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Site;
use App\Models\Space;
use App\Services\ActivityService;
use App\Services\AuditService;
use App\Services\RemotePolicyResolver;
use App\Services\UsageAggregationService;
use App\Enums\EventSource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LocationsController extends Controller
{
    public function __construct(
        private UsageAggregationService $usage,
        private RemotePolicyResolver $policies,
        private ActivityService $activity,
        private AuditService $audit,
    ) {}

    public function index(Request $request)
    {
        $organization = $request->attributes->get('organization');
        $today = now($organization->timezone ?: 'UTC')->startOfDay();

        $sites = Site::where('organization_id', $organization->id)
            ->with(['buildings' => fn ($q) => $q->orderBy('name'), 'buildings.floors' => fn ($q) => $q->orderBy('floor_number')->orderBy('name'), 'buildings.floors.spaces' => fn ($q) => $q->whereNull('parent_space_id')->orderBy('name'), 'buildings.floors.spaces.childrenRecursive'])
            ->orderBy('name')
            ->get();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $sites = $this->filterTree($sites, $q);
        }

        $selectedType = (string) $request->query('type', 'site');
        $selectedId = (int) $request->query('id');
        $selected = null;

        if ($selectedId && in_array($selectedType, ['site', 'building', 'floor', 'space'], true)) {
            $selected = $this->findNode($organization->id, $selectedType, $selectedId);
        }

        if (! $selected) {
            $selected = $sites->first();
            $selectedType = 'site';
        }

        if (! $selected) {
            return view('locations', [
                'organization' => $organization,
                'sites' => $sites,
                'selected' => null,
                'selectedType' => null,
                'q' => $q,
                'spaces' => collect(),
                'acs' => collect(),
                'summary' => ['total' => 0, 'on' => 0, 'off' => 0, 'unknown' => 0, 'today_seconds' => 0],
                'breadcrumb' => [],
                'policy' => null,
                'policyScope' => null,
                'policyScopeId' => null,
                'allSites' => collect(),
                'allBuildings' => collect(),
                'allFloors' => collect(),
                'allSpaces' => collect(),
                'selectedId' => null,
            ]);
        }

        $selectedId = $selected->id;
        $spaceIds = $this->spaceIdsForSelected($selectedType, $selected);

        $acs = AcUnit::where('organization_id', $organization->id)
            ->whereIn('space_id', $spaceIds)
            ->with(['stateSnapshot', 'currentAssignment.device', 'space.floor.building.site', 'acModel'])
            ->get();

        $todayUsage = DB::table('ac_daily_usage')
            ->where('organization_id', $organization->id)
            ->where('usage_date', $today->toDateString())
            ->whereIn('ac_unit_id', $acs->pluck('id'))
            ->pluck('on_seconds', 'ac_unit_id');

        $acsBySpace = $acs->groupBy('space_id');
        $spaces = $this->spacesForSelected($organization->id, $selectedType, $selected)
            ->map(function (Space $space) use ($acsBySpace, $todayUsage) {
                $group = $acsBySpace->get($space->id, collect());
                $on = $group->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOn && ! $ac->stateSnapshot->isStale())->count();
                $off = $group->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOff && ! $ac->stateSnapshot->isStale())->count();

                return [
                    'model' => $space,
                    'acs_count' => $group->count(),
                    'on' => $on,
                    'off' => $off,
                    'unknown' => max(0, $group->count() - $on - $off),
                    'today_seconds' => $group->sum(fn (AcUnit $ac) => (int) ($todayUsage[$ac->id] ?? 0)),
                ];
            });

        $on = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOn && ! $ac->stateSnapshot->isStale())->count();
        $off = $acs->filter(fn (AcUnit $ac) => $ac->stateSnapshot?->power_state === PowerState::KnownOff && ! $ac->stateSnapshot->isStale())->count();

        $policy = null;
        $policyScope = match ($selectedType) {
            'site' => PolicyScope::Site,
            'building' => PolicyScope::Building,
            'floor' => PolicyScope::Floor,
            'space' => PolicyScope::Space,
        };
        $policy = $this->policies->effectiveForScope($organization, $policyScope, $selected->id);

        $allSpaces = Space::where('organization_id', $organization->id)
            ->with('floor.building')
            ->orderBy('name')
            ->get();

        return view('locations', [
            'organization' => $organization,
            'sites' => $sites,
            'selected' => $selected,
            'selectedType' => $selectedType,
            'selectedId' => $selectedId,
            'q' => $q,
            'spaces' => $spaces,
            'acs' => $acs,
            'summary' => [
                'total' => $acs->count(),
                'on' => $on,
                'off' => $off,
                'unknown' => max(0, $acs->count() - $on - $off),
                'today_seconds' => (int) $todayUsage->sum(),
            ],
            'breadcrumb' => $this->breadcrumb($selectedType, $selected),
            'policy' => $policy,
            'policyScope' => $policyScope->value,
            'policyScopeId' => $selected->id,
            'allSites' => $organization->sites()->orderBy('name')->get(),
            'allBuildings' => Building::where('organization_id', $organization->id)->with('site')->orderBy('name')->get(),
            'allFloors' => Floor::where('organization_id', $organization->id)->with('building')->orderBy('name')->get(),
            'allSpaces' => $allSpaces,
        ]);
    }

    public function store(\App\Http\Requests\StoreLocationRequest $request)
    {
        $organization = $request->attributes->get('organization');
        $type = $request->input('location_type');

        $model = match ($type) {
            'site' => new Site(['organization_id' => $organization->id, 'address' => $request->input('address'), 'timezone' => $request->input('timezone')]),
            'building' => new Building(['organization_id' => $organization->id, 'site_id' => $request->integer('site_id')]),
            'floor' => new Floor(['organization_id' => $organization->id, 'building_id' => $request->integer('building_id'), 'floor_number' => $request->input('floor_number')]),
            'space' => new Space([
                'organization_id' => $organization->id,
                'floor_id' => $request->integer('floor_id'),
                'parent_space_id' => $request->integer('parent_space_id') ?: null,
                'space_type' => $request->input('space_type', 'room'),
                'area_sqm' => $request->input('area_sqm'),
            ]),
        };

        $model->fill([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
        ]);

        if ($type === 'site') {
            $model->status = 'active';
        }
        if (isset($model->status) === false) {
            $model->status = 'active';
        }

        $model->save();

        $this->audit->record($organization, $request->user(), 'location.created', $type, $model->id, null, $model->toArray(), $request);
        $this->activity->record($organization, null, EventSource::Portal, 'location_created', "Created {$type} \"{$model->name}\".", 'success', $request->user());

        return redirect()->route('locations.index', ['type' => $type, 'id' => $model->id])
            ->with('success', ucfirst($type).' created.');
    }

    public function update(\App\Http\Requests\UpdateLocationRequest $request, string $locationType, int $id)
    {
        $organization = $request->attributes->get('organization');
        $model = $this->findNode($organization->id, $locationType, $id);

        if (! $model) {
            abort(404);
        }

        $before = $model->toArray();
        $payload = $request->safe()->only(['name', 'code', 'address', 'timezone', 'status']);

        if ($locationType === 'floor') {
            $payload['floor_number'] = $request->input('floor_number');
        }
        if ($locationType === 'space') {
            $payload['space_type'] = $request->input('space_type', $model->space_type);
            $payload['area_sqm'] = $request->input('area_sqm');
        }

        $model->update($payload);

        $this->audit->record($organization, $request->user(), 'location.updated', $locationType, $model->id, $before, $model->fresh()->toArray(), $request);
        $this->activity->record($organization, null, EventSource::Portal, 'location_updated', "Updated {$locationType} \"{$model->name}\".", 'success', $request->user());

        return redirect()->route('locations.index', ['type' => $locationType, 'id' => $model->id])
            ->with('success', 'Location updated.');
    }

    public function destroy(Request $request, string $locationType, int $id)
    {
        $organization = $request->attributes->get('organization');
        $model = $this->findNode($organization->id, $locationType, $id);

        if (! $model) {
            abort(404);
        }

        $hasChildren = match ($locationType) {
            'site' => $model->buildings()->exists(),
            'building' => $model->floors()->exists(),
            'floor' => $model->spaces()->exists(),
            'space' => $model->children()->exists() || $model->acUnits()->exists(),
        };

        if ($hasChildren) {
            return back()->with('error', 'This location still contains child locations or air conditioners and cannot be deleted.');
        }

        $name = $model->name;
        $model->delete();

        $this->audit->record($organization, $request->user(), 'location.deleted', $locationType, $id, ['name' => $name], null, $request);
        $this->activity->record($organization, null, EventSource::Portal, 'location_deleted', "Deleted {$locationType} \"{$name}\".", 'success', $request->user());

        return redirect()->route('locations.index')->with('success', 'Location deleted.');
    }

    private function findNode(int $organizationId, string $type, int $id)
    {
        return match ($type) {
            'site' => Site::where('organization_id', $organizationId)->find($id),
            'building' => Building::where('organization_id', $organizationId)->find($id),
            'floor' => Floor::where('organization_id', $organizationId)->find($id),
            'space' => Space::where('organization_id', $organizationId)->find($id),
            default => null,
        };
    }

    private function spaceIdsForSelected(string $type, $selected): array
    {
        return match ($type) {
            'site' => Space::where('organization_id', $selected->organization_id)
                ->whereHas('floor.building', fn ($q) => $q->where('site_id', $selected->id))->pluck('id')->all(),
            'building' => Space::where('organization_id', $selected->organization_id)
                ->whereHas('floor', fn ($q) => $q->where('building_id', $selected->id))->pluck('id')->all(),
            'floor' => Space::where('organization_id', $selected->organization_id)
                ->where('floor_id', $selected->id)->pluck('id')->all(),
            'space' => $selected->allSpaceIds(),
        };
    }

    private function spacesForSelected(int $organizationId, string $type, $selected)
    {
        return match ($type) {
            'site' => Space::where('organization_id', $organizationId)
                ->whereHas('floor.building', fn ($q) => $q->where('site_id', $selected->id))
                ->with('floor.building')->orderBy('name')->get(),
            'building' => Space::where('organization_id', $organizationId)
                ->whereHas('floor', fn ($q) => $q->where('building_id', $selected->id))
                ->with('floor.building')->orderBy('name')->get(),
            'floor' => Space::where('organization_id', $organizationId)
                ->where('floor_id', $selected->id)->whereNull('parent_space_id')
                ->with('floor.building')->orderBy('name')->get(),
            'space' => Space::where('organization_id', $organizationId)
                ->where('parent_space_id', $selected->id)->with('floor.building')->orderBy('name')->get(),
        };
    }

    private function breadcrumb(string $type, $selected): array
    {
        if ($type === 'space') {
            return $selected->breadcrumb();
        }

        $nodes = [];
        if ($type === 'site') {
            $nodes[] = ['label' => $selected->name, 'type' => 'site', 'id' => $selected->id];
        } elseif ($type === 'building') {
            if ($selected->site) {
                $nodes[] = ['label' => $selected->site->name, 'type' => 'site', 'id' => $selected->site->id];
            }
            $nodes[] = ['label' => $selected->name, 'type' => 'building', 'id' => $selected->id];
        } elseif ($type === 'floor') {
            if ($selected->building?->site) {
                $nodes[] = ['label' => $selected->building->site->name, 'type' => 'site', 'id' => $selected->building->site->id];
            }
            if ($selected->building) {
                $nodes[] = ['label' => $selected->building->name, 'type' => 'building', 'id' => $selected->building->id];
            }
            $nodes[] = ['label' => $selected->name, 'type' => 'floor', 'id' => $selected->id];
        }

        return $nodes;
    }

    private function filterTree($sites, string $q)
    {
        $needle = mb_strtolower($q);

        return $sites->map(function (Site $site) use ($needle) {
            $site->setRelation('buildings', $site->buildings->map(function (Building $building) use ($needle) {
                $building->setRelation('floors', $building->floors->map(function (Floor $floor) use ($needle) {
                    $floor->setRelation('spaces', $this->filterSpaces($floor->spaces, $needle));
                    return $floor;
                })->filter(fn (Floor $floor) => $floor->spaces->isNotEmpty() || str_contains(mb_strtolower($floor->name), $needle))->values());
                return $building;
            })->filter(fn (Building $building) => $building->floors->isNotEmpty() || str_contains(mb_strtolower($building->name), $needle))->values());
            return $site;
        })->filter(fn (Site $site) => $site->buildings->isNotEmpty() || str_contains(mb_strtolower($site->name), $needle))->values();
    }

    private function filterSpaces($spaces, string $needle)
    {
        return $spaces->map(function (Space $space) use ($needle) {
            $space->setRelation('childrenRecursive', $this->filterSpaces($space->childrenRecursive, $needle));
            return $space;
        })->filter(fn (Space $space) => $space->childrenRecursive->isNotEmpty() || str_contains(mb_strtolower($space->name), $needle))->values();
    }
}
