<?php

namespace App\Services;

use App\Models\AcDailyUsage;
use App\Models\AcUnit;
use App\Models\Organization;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UsageAggregationService
{
    public function __construct(private OnDurationCalculator $calculator) {}

    public function rebuildDay(Organization $organization, CarbonInterface $localDate): void
    {
        $tz = $organization->timezone ?: 'UTC';
        $start = Carbon::parse($localDate->toDateString(), $tz)->startOfDay()->utc();
        $end = $start->copy()->addDay();

        AcUnit::where('organization_id', $organization->id)
            ->with(['stateSnapshot', 'stateHistory'])
            ->chunkById(100, function ($acs) use ($organization, $localDate, $start, $end) {
                foreach ($acs as $ac) {
                    $result = $this->calculator->calculate($ac, $start, $end);

                    AcDailyUsage::updateOrCreate(
                        ['ac_unit_id' => $ac->id, 'usage_date' => $localDate->toDateString()],
                        [
                            'organization_id' => $organization->id,
                            'on_seconds' => $result['on_seconds'],
                            'unknown_seconds' => $result['unknown_seconds'],
                            'incomplete' => $result['incomplete'],
                            'calculated_at' => now(),
                        ]
                    );
                }
            });
    }

    public function rebuildRange(Organization $organization, CarbonInterface $from, CarbonInterface $to): void
    {
        $cursor = Carbon::parse($from->toDateString(), $organization->timezone ?: 'UTC')->startOfDay();

        while ($cursor->lte($to)) {
            $this->rebuildDay($organization, $cursor->copy());
            $cursor->addDay();
        }
    }

    public function rebuildForAc(AcUnit $ac, CarbonInterface $at): void
    {
        $organization = $ac->organization;
        $tz = $organization->timezone ?: 'UTC';
        $local = Carbon::instance($at)->setTimezone($tz);

        $this->rebuildDay($organization, $local->copy()->startOfDay());
        if ($local->hour === 0) {
            $this->rebuildDay($organization, $local->copy()->subDay()->startOfDay());
        }
    }

    public function ensureToday(Organization $organization): void
    {
        $today = now()->setTimezone($organization->timezone ?: 'UTC')->startOfDay();
        $exists = AcDailyUsage::where('organization_id', $organization->id)
            ->where('usage_date', $today->toDateString())
            ->whereNotNull('calculated_at')
            ->exists();

        if (! $exists) {
            $this->rebuildDay($organization, $today);
        }
    }

    public function baseQuery(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): Builder
    {
        $query = AcDailyUsage::query()
            ->from('ac_daily_usage as usage')
            ->join('ac_units as acs', 'acs.id', '=', 'usage.ac_unit_id')
            ->join('spaces', 'spaces.id', '=', 'acs.space_id')
            ->join('floors', 'floors.id', '=', 'spaces.floor_id')
            ->join('buildings', 'buildings.id', '=', 'floors.building_id')
            ->join('sites', 'sites.id', '=', 'buildings.site_id')
            ->where('usage.organization_id', $organization->id)
            ->whereBetween('usage.usage_date', [$from->toDateString(), $to->toDateString()]);

        $this->applyFilters($query, $filters);

        return $query;
    }

    public function applyFilters(Builder $query, array $filters = []): void
    {
        if (! empty($filters['site_id'])) {
            $query->where('sites.id', $filters['site_id']);
        }
        if (! empty($filters['building_id'])) {
            $query->where('buildings.id', $filters['building_id']);
        }
        if (! empty($filters['floor_id'])) {
            $query->where('floors.id', $filters['floor_id']);
        }
        if (! empty($filters['space_id'])) {
            $query->where('spaces.id', $filters['space_id']);
        }
        if (! empty($filters['ac_id'])) {
            $query->where('acs.id', $filters['ac_id']);
        }
        if (! empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($q) use ($term) {
                $q->where('acs.name', 'like', $term)
                    ->orWhere('acs.asset_code', 'like', $term)
                    ->orWhere('spaces.name', 'like', $term);
            });
        }
    }

    public function totals(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): array
    {
        $row = $this->baseQuery($organization, $from, $to, $filters)
            ->selectRaw('COALESCE(SUM(usage.on_seconds), 0) as on_seconds')
            ->selectRaw('COALESCE(SUM(usage.unknown_seconds), 0) as unknown_seconds')
            ->selectRaw('SUM(CASE WHEN usage.incomplete = 1 THEN 1 ELSE 0 END) as incomplete_rows')
            ->selectRaw('COUNT(DISTINCT usage.ac_unit_id) as ac_count')
            ->first();

        return [
            'on_seconds' => (int) ($row->on_seconds ?? 0),
            'unknown_seconds' => (int) ($row->unknown_seconds ?? 0),
            'incomplete_rows' => (int) ($row->incomplete_rows ?? 0),
            'ac_count' => (int) ($row->ac_count ?? 0),
        ];
    }

    public function dailySeries(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): array
    {
        $rows = $this->baseQuery($organization, $from, $to, $filters)
            ->selectRaw('usage.usage_date as day')
            ->selectRaw('SUM(usage.on_seconds) as on_seconds')
            ->groupBy('usage.usage_date')
            ->orderBy('usage.usage_date')
            ->get();

        $map = $rows->mapWithKeys(fn ($row) => [
            Carbon::parse($row->day)->toDateString() => (int) $row->on_seconds,
        ]);

        $series = [];
        $cursor = Carbon::parse($from->toDateString());
        $end = Carbon::parse($to->toDateString());

        while ($cursor->lte($end)) {
            $series[] = [
                'date' => $cursor->toDateString(),
                'label' => $cursor->format('d M'),
                'seconds' => $map->get($cursor->toDateString(), 0),
            ];
            $cursor->addDay();
        }

        return $series;
    }

    public function byAc(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = [], int $limit = 50): Collection
    {
        return $this->baseQuery($organization, $from, $to, $filters)
            ->select(['acs.id', 'acs.name', 'acs.asset_code', 'spaces.name as space_name', 'buildings.name as building_name', 'sites.name as site_name'])
            ->selectRaw('SUM(usage.on_seconds) as on_seconds')
            ->selectRaw('SUM(usage.unknown_seconds) as unknown_seconds')
            ->selectRaw('SUM(CASE WHEN usage.incomplete = 1 THEN 1 ELSE 0 END) as incomplete_rows')
            ->groupBy('acs.id', 'acs.name', 'acs.asset_code', 'spaces.name', 'buildings.name', 'sites.name')
            ->orderByDesc('on_seconds')
            ->limit($limit)
            ->get();
    }

    public function byBuilding(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): Collection
    {
        return $this->baseQuery($organization, $from, $to, $filters)
            ->select(['buildings.id', 'buildings.name as building_name', 'sites.name as site_name'])
            ->selectRaw('SUM(usage.on_seconds) as on_seconds')
            ->selectRaw('COUNT(DISTINCT usage.ac_unit_id) as ac_count')
            ->groupBy('buildings.id', 'buildings.name', 'sites.name')
            ->orderByDesc('on_seconds')
            ->get();
    }

    public function byFloor(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): Collection
    {
        return $this->baseQuery($organization, $from, $to, $filters)
            ->select(['floors.id', 'floors.name as floor_name', 'buildings.name as building_name'])
            ->selectRaw('SUM(usage.on_seconds) as on_seconds')
            ->selectRaw('COUNT(DISTINCT usage.ac_unit_id) as ac_count')
            ->groupBy('floors.id', 'floors.name', 'buildings.name')
            ->orderByDesc('on_seconds')
            ->get();
    }

    public function bySpace(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): Collection
    {
        return $this->baseQuery($organization, $from, $to, $filters)
            ->select(['spaces.id', 'spaces.name as space_name', 'spaces.space_type', 'floors.name as floor_name', 'buildings.name as building_name'])
            ->selectRaw('SUM(usage.on_seconds) as on_seconds')
            ->selectRaw('COUNT(DISTINCT usage.ac_unit_id) as ac_count')
            ->groupBy('spaces.id', 'spaces.name', 'spaces.space_type', 'floors.name', 'buildings.name')
            ->orderByDesc('on_seconds')
            ->get();
    }

    public function mostUsedLocation(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): ?object
    {
        return $this->bySpace($organization, $from, $to, $filters)->first();
    }

    public function averageSetpoint(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): ?float
    {
        $query = DB::table('ac_state_history as h')
            ->join('ac_units as acs', 'acs.id', '=', 'h.ac_unit_id')
            ->join('spaces', 'spaces.id', '=', 'acs.space_id')
            ->join('floors', 'floors.id', '=', 'spaces.floor_id')
            ->join('buildings', 'buildings.id', '=', 'floors.building_id')
            ->join('sites', 'sites.id', '=', 'buildings.site_id')
            ->where('h.organization_id', $organization->id)
            ->where('h.state_field', 'temperature_setpoint_c')
            ->whereBetween('h.observed_at', [$from, $to]);

        foreach (['site_id' => 'sites.id', 'building_id' => 'buildings.id', 'floor_id' => 'floors.id', 'space_id' => 'spaces.id', 'ac_id' => 'acs.id'] as $key => $column) {
            if (! empty($filters[$key])) {
                $query->where($column, $filters[$key]);
            }
        }

        $rows = $query->pluck('h.new_value');

        $values = $rows->map(function ($value) {
            if (is_array($value)) {
                return $value['temperature_setpoint_c'] ?? $value['value'] ?? null;
            }

            return is_numeric($value) ? (float) $value : null;
        })->filter(fn ($value) => $value !== null && $value >= 10 && $value <= 35);

        return $values->isEmpty() ? null : round($values->avg(), 1);
    }

    public function setpointSeries(Organization $organization, CarbonInterface $from, CarbonInterface $to, array $filters = []): array
    {
        $query = DB::table('ac_state_history as h')
            ->join('ac_units as acs', 'acs.id', '=', 'h.ac_unit_id')
            ->join('spaces', 'spaces.id', '=', 'acs.space_id')
            ->join('floors', 'floors.id', '=', 'spaces.floor_id')
            ->join('buildings', 'buildings.id', '=', 'floors.building_id')
            ->join('sites', 'sites.id', '=', 'buildings.site_id')
            ->where('h.organization_id', $organization->id)
            ->where('h.state_field', 'temperature_setpoint_c')
            ->whereBetween('h.observed_at', [$from, $to])
            ->orderBy('h.observed_at');

        foreach (['site_id' => 'sites.id', 'building_id' => 'buildings.id', 'floor_id' => 'floors.id', 'space_id' => 'spaces.id', 'ac_id' => 'acs.id'] as $key => $column) {
            if (! empty($filters[$key])) {
                $query->where($column, $filters[$key]);
            }
        }

        return $query->get(['h.observed_at', 'h.new_value'])
            ->map(function ($row) {
                $value = is_array($row->new_value)
                    ? ($row->new_value['temperature_setpoint_c'] ?? $row->new_value['value'] ?? null)
                    : $row->new_value;

                return [
                    'at' => Carbon::parse($row->observed_at)->toIso8601String(),
                    'value' => is_numeric($value) ? (float) $value : null,
                ];
            })
            ->filter(fn ($row) => $row['value'] !== null)
            ->values()
            ->all();
    }
}
