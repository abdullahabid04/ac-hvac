<?php

namespace App\Services;

use App\Enums\EventSource;
use App\Enums\PolicyScope;
use App\Enums\PolicyValue;
use App\Models\AcUnit;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Organization;
use App\Models\RemotePolicy;
use App\Models\Site;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemotePolicyResolver
{
    public function effective(AcUnit $ac): array
    {
        $ac->loadMissing(['space.parent.parent.parent.parent', 'organization']);

        $checks = [['ac', $ac->id, 'AC override']];

        $space = $ac->space;
        $depth = 0;
        while ($space && $depth < 10) {
            $checks[] = ['space', $space->id, $space->name];
            $space = $space->parent;
            $depth++;
        }

        $floor = $ac->space?->floor;
        if ($floor) {
            $checks[] = ['floor', $floor->id, $floor->name];
            $building = $floor->building;
            if ($building) {
                $checks[] = ['building', $building->id, $building->name];
                if ($building->site) {
                    $checks[] = ['site', $building->site->id, $building->site->name];
                }
            }
        }

        $checks[] = ['organization', $ac->organization_id, 'Organization'];

        foreach ($checks as [$scopeType, $scopeId, $label]) {
            $policy = RemotePolicy::query()
                ->where('organization_id', $ac->organization_id)
                ->where('scope_type', $scopeType)
                ->where('scope_id', $scopeId)
                ->first();

            if ($policy && $policy->policy_value !== PolicyValue::Inherit) {
                return [
                    'value' => $policy->policy_value,
                    'source_scope' => $scopeType,
                    'source_id' => $scopeId,
                    'source_label' => $label,
                    'version' => $policy->version,
                    'policy_id' => $policy->id,
                ];
            }
        }

        $default = PolicyValue::from(config('hvac.default_remote_policy', 'disabled'));

        return [
            'value' => $default,
            'source_scope' => 'default',
            'source_id' => null,
            'source_label' => 'System default (provisional)',
            'version' => 1,
            'policy_id' => null,
        ];
    }

    public function effectiveForScope(Organization $organization, PolicyScope $scope, int $scopeId): array
    {
        if ($scope === PolicyScope::Ac) {
            $ac = AcUnit::where('organization_id', $organization->id)->find($scopeId);
            if ($ac) {
                return $this->effective($ac);
            }
        }

        $checks = [];

        if ($scope === PolicyScope::Space) {
            $space = Space::where('organization_id', $organization->id)->find($scopeId);
            $depth = 0;
            while ($space && $depth < 10) {
                $checks[] = ['space', $space->id, $space->name];
                $space = $space->parent;
                $depth++;
            }
            $space = Space::where('organization_id', $organization->id)->find($scopeId);
            if ($space?->floor) {
                $checks[] = ['floor', $space->floor->id, $space->floor->name];
                if ($space->floor->building) {
                    $checks[] = ['building', $space->floor->building->id, $space->floor->building->name];
                    if ($space->floor->building->site) {
                        $checks[] = ['site', $space->floor->building->site->id, $space->floor->building->site->name];
                    }
                }
            }
        } elseif ($scope === PolicyScope::Floor) {
            $floor = Floor::where('organization_id', $organization->id)->find($scopeId);
            if ($floor) {
                $checks[] = ['floor', $floor->id, $floor->name];
                if ($floor->building) {
                    $checks[] = ['building', $floor->building->id, $floor->building->name];
                    if ($floor->building->site) {
                        $checks[] = ['site', $floor->building->site->id, $floor->building->site->name];
                    }
                }
            }
        } elseif ($scope === PolicyScope::Building) {
            $building = Building::where('organization_id', $organization->id)->find($scopeId);
            if ($building) {
                $checks[] = ['building', $building->id, $building->name];
                if ($building->site) {
                    $checks[] = ['site', $building->site->id, $building->site->name];
                }
            }
        } elseif ($scope === PolicyScope::Site) {
            $site = Site::where('organization_id', $organization->id)->find($scopeId);
            if ($site) {
                $checks[] = ['site', $site->id, $site->name];
            }
        }

        $checks[] = ['organization', $organization->id, 'Organization'];

        foreach ($checks as [$scopeType, $id, $label]) {
            $policy = RemotePolicy::where('organization_id', $organization->id)
                ->where('scope_type', $scopeType)
                ->where('scope_id', $id)
                ->first();

            if ($policy && $policy->policy_value !== PolicyValue::Inherit) {
                return [
                    'value' => $policy->policy_value,
                    'source_scope' => $scopeType,
                    'source_id' => $id,
                    'source_label' => $label,
                    'version' => $policy->version,
                    'policy_id' => $policy->id,
                ];
            }
        }

        return [
            'value' => PolicyValue::from(config('hvac.default_remote_policy', 'disabled')),
            'source_scope' => 'default',
            'source_id' => null,
            'source_label' => 'System default (provisional)',
            'version' => 1,
            'policy_id' => null,
        ];
    }

    public function setPolicy(
        Organization $organization,
        PolicyScope $scope,
        int $scopeId,
        PolicyValue $value,
        ?User $actor = null,
    ): RemotePolicy {
        $this->assertScopeBelongsToOrganization($organization, $scope, $scopeId);

        return DB::transaction(function () use ($organization, $scope, $scopeId, $value, $actor) {
            $policy = RemotePolicy::query()
                ->where('organization_id', $organization->id)
                ->where('scope_type', $scope->value)
                ->where('scope_id', $scopeId)
                ->first();

            $before = $policy?->only(['policy_value', 'version']);
            $version = $policy && $policy->policy_value !== $value ? $policy->version + 1 : ($policy?->version ?? 1);

            if (! $policy) {
                $policy = new RemotePolicy([
                    'organization_id' => $organization->id,
                    'scope_type' => $scope->value,
                    'scope_id' => $scopeId,
                ]);
            }

            $policy->fill([
                'policy_value' => $value,
                'version' => $version,
                'updated_by' => $actor?->id,
            ])->save();

            $this->syncAffectedDevices($organization, $scope, $scopeId);

            app(AuditService::class)->record(
                $organization,
                $actor,
                'remote_policy.updated',
                $scope->value,
                $scopeId,
                $before,
                ['policy_value' => $value->value, 'version' => $version],
            );

            app(ActivityService::class)->record(
                $organization,
                $scope === PolicyScope::Ac ? AcUnit::find($scopeId) : null,
                EventSource::Portal,
                'policy_updated',
                "Physical remote policy set to {$value->label()} for {$this->scopeLabel($scope, $scopeId)}.",
                'success',
                $actor,
                occurredAt: now(),
            );

            return $policy->refresh();
        });
    }

    public function assertScopeBelongsToOrganization(Organization $organization, PolicyScope $scope, int $scopeId): void
    {
        $exists = match ($scope) {
            PolicyScope::Organization => $organization->id === $scopeId,
            PolicyScope::Site => Site::where('organization_id', $organization->id)->whereKey($scopeId)->exists(),
            PolicyScope::Building => Building::where('organization_id', $organization->id)->whereKey($scopeId)->exists(),
            PolicyScope::Floor => Floor::where('organization_id', $organization->id)->whereKey($scopeId)->exists(),
            PolicyScope::Space => Space::where('organization_id', $organization->id)->whereKey($scopeId)->exists(),
            PolicyScope::Ac => AcUnit::where('organization_id', $organization->id)->whereKey($scopeId)->exists(),
        };

        if (! $exists) {
            throw ValidationException::withMessages(['scope' => 'The selected policy scope does not belong to this organization.']);
        }
    }

    public function scopeLabel(PolicyScope $scope, int $scopeId): string
    {
        $name = match ($scope) {
            PolicyScope::Organization => Organization::find($scopeId)?->name,
            PolicyScope::Site => Site::find($scopeId)?->name,
            PolicyScope::Building => Building::find($scopeId)?->name,
            PolicyScope::Floor => Floor::find($scopeId)?->name,
            PolicyScope::Space => Space::find($scopeId)?->name,
            PolicyScope::Ac => AcUnit::find($scopeId)?->name,
        };

        return trim($scope->label().' — '.($name ?? "#{$scopeId}"), ' —');
    }

    public function affectedAcUnits(Organization $organization, PolicyScope $scope, int $scopeId)
    {
        return match ($scope) {
            PolicyScope::Organization => AcUnit::where('organization_id', $organization->id)->get(),
            PolicyScope::Site => AcUnit::where('organization_id', $organization->id)
                ->whereHas('space.floor.building', fn ($q) => $q->where('site_id', $scopeId))->get(),
            PolicyScope::Building => AcUnit::where('organization_id', $organization->id)
                ->whereHas('space.floor', fn ($q) => $q->where('building_id', $scopeId))->get(),
            PolicyScope::Floor => AcUnit::where('organization_id', $organization->id)
                ->whereHas('space', fn ($q) => $q->where('floor_id', $scopeId))->get(),
            PolicyScope::Space => $this->acUnitsForSpaceTree($organization, $scopeId),
            PolicyScope::Ac => AcUnit::where('organization_id', $organization->id)->whereKey($scopeId)->get(),
        };
    }

    private function acUnitsForSpaceTree(Organization $organization, int $spaceId)
    {
        $space = Space::where('organization_id', $organization->id)->whereKey($spaceId)->first();

        if (! $space) {
            return AcUnit::whereRaw('1 = 0')->get();
        }

        return AcUnit::where('organization_id', $organization->id)
            ->whereIn('space_id', $space->allSpaceIds())
            ->get();
    }

    public function syncAffectedDevices(Organization $organization, PolicyScope $scope, int $scopeId): void
    {
        foreach ($this->affectedAcUnits($organization, $scope, $scopeId) as $ac) {
            $ac->loadMissing('currentAssignment.device', 'space.parent.parent.parent.parent');
            $device = $ac->device();

            if (! $device) {
                continue;
            }

            $effective = $this->effective($ac);

            $device->update([
                'effective_remote_policy' => $effective['value']->value,
                'policy_version' => $device->policy_version + 1,
                'policy_synced_at' => null,
            ]);
        }
    }

    public function syncDevice(Device $device): void
    {
        $ac = $device->acUnit();

        if (! $ac) {
            return;
        }

        $effective = $this->effective($ac);

        $device->update([
            'effective_remote_policy' => $effective['value']->value,
            'policy_version' => $device->policy_version + 1,
            'policy_synced_at' => null,
        ]);
    }
}
