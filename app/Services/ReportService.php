<?php

namespace App\Services;

use App\Enums\ConnectivityStatus;
use App\Enums\EventSource;
use App\Enums\PolicyValue;
use App\Models\AcUnit;
use App\Models\Device;
use App\Models\GeneratedReport;
use App\Models\Organization;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ReportService
{
    public function __construct(
        private UsageAggregationService $usage,
        private OnDurationCalculator $calculator,
    ) {}

    public function request(Organization $organization, User $user, string $type, array $filters, string $format = 'pdf'): GeneratedReport
    {
        $title = config('hvac.report_types')[$type] ?? 'Operational Report';

        $report = GeneratedReport::create([
            'organization_id' => $organization->id,
            'requested_by' => $user->id,
            'report_type' => $type,
            'title' => $title,
            'filters' => $filters,
            'format' => $format,
            'status' => 'processing',
            'expires_at' => now()->addDays(30),
        ]);

        try {
            $data = $this->build($organization, $type, $filters);
            $extension = $format === 'csv' ? 'csv' : 'pdf';
            $path = "reports/{$organization->id}/{$report->id}.{$extension}";

            if ($format === 'csv') {
                Storage::disk('local')->put($path, $this->csv($data));
            } else {
                $pdf = Pdf::loadView('reports.pdf', $data)->setPaper('a4', 'landscape');
                Storage::disk('local')->put($path, $pdf->output());
            }

            $report->update([
                'status' => 'completed',
                'file_path' => $path,
                'completed_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $report->update([
                'status' => 'failed',
                'error_message' => 'Report generation failed. Please check the server log.',
            ]);
        }

        return $report->refresh();
    }

    public function build(Organization $organization, string $type, array $filters): array
    {
        $tz = $organization->timezone ?: 'UTC';
        $from = Carbon::parse($filters['from'] ?? now($tz)->toDateString(), $tz)->startOfDay();
        $to = Carbon::parse($filters['to'] ?? now($tz)->toDateString(), $tz)->endOfDay();

        if ($to->lt($from)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $scope = $this->scopeLabel($organization, $filters);

        $data = match ($type) {
            'daily_usage' => $this->dailyUsage($organization, $from, $to, $filters),
            'weekly_usage' => $this->periodUsage($organization, $from, $to, $filters, 'week'),
            'monthly_usage' => $this->periodUsage($organization, $from, $to, $filters, 'month'),
            'location_usage' => $this->locationUsage($organization, $from, $to, $filters),
            'ac_history' => $this->stateHistory($organization, $from, $to, $filters, 'power_state'),
            'setpoint_history' => $this->stateHistory($organization, $from, $to, $filters, 'temperature_setpoint_c'),
            'device_connectivity' => $this->deviceConnectivity($organization),
            default => throw new \InvalidArgumentException('Unsupported report type.'),
        };

        $data['organization'] = $organization;
        $data['period'] = $from->format('d M Y').' – '.$to->format('d M Y');
        $data['scope'] = $scope;
        $data['timezone'] = $tz;
        $data['generated_at'] = now($tz)->format('d M Y, H:i');
        $data['notes'] = [
            'ON duration is operating time derived from known ON/OFF transitions, not electricity consumption (kWh).',
            'Unknown or stale intervals are never counted as confirmed OFF time.',
            'Setpoint is the AC target temperature, not measured room temperature.',
            'IR transmission success does not prove physical AC execution.',
            'Sample development data is illustrative and is not production evidence.',
        ];

        return $data;
    }

    private function dailyUsage(Organization $organization, Carbon $from, Carbon $to, array $filters): array
    {
        $series = $this->usage->dailySeries($organization, $from, $to, $filters);
        $rows = collect($series)->map(fn ($row) => [
            'date' => Carbon::parse($row['date'])->format('d M Y'),
            'on_duration' => $this->hours($row['seconds']),
            'incomplete' => $row['seconds'] > 0 ? 'Check intervals' : '—',
        ])->all();

        return [
            'title' => 'Daily Usage Report',
            'columns' => ['date' => 'Date', 'on_duration' => 'Known ON duration', 'incomplete' => 'Data note'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total known ON duration', 'value' => $this->hours(array_sum(array_column($series, 'seconds')))],
                ['label' => 'Days in range', 'value' => count($series)],
            ],
        ];
    }

    private function periodUsage(Organization $organization, Carbon $from, Carbon $to, array $filters, string $period): array
    {
        $series = $this->usage->dailySeries($organization, $from, $to, $filters);
        $grouped = collect($series)->groupBy(function ($row) use ($period) {
            $date = Carbon::parse($row['date']);

            return $period === 'week' ? $date->format('o-W') : $date->format('Y-m');
        })->map(function ($rows, $key) use ($period) {
            $first = Carbon::parse($rows->first()['date']);

            return [
                'period' => $period === 'week' ? 'Week '.$first->format('W, Y') : $first->format('F Y'),
                'on_duration' => $this->hours($rows->sum('seconds')),
                'days' => $rows->count(),
            ];
        })->values()->all();

        return [
            'title' => ucfirst($period).'ly Usage Report',
            'columns' => ['period' => ucfirst($period), 'on_duration' => 'Known ON duration', 'days' => 'Days covered'],
            'rows' => $grouped,
            'summary' => [
                ['label' => 'Total known ON duration', 'value' => $this->hours(array_sum(array_column($series, 'seconds')))],
                ['label' => 'Periods', 'value' => count($grouped)],
            ],
        ];
    }

    private function locationUsage(Organization $organization, Carbon $from, Carbon $to, array $filters): array
    {
        $rows = $this->usage->bySpace($organization, $from, $to, $filters)->map(fn ($row) => [
            'location' => implode(' › ', array_filter([$row->building_name, $row->floor_name, $row->space_name])),
            'type' => ucfirst($row->space_type),
            'acs' => $row->ac_count,
            'on_duration' => $this->hours((int) $row->on_seconds),
        ])->all();

        return [
            'title' => 'Location-wise Usage Report',
            'columns' => ['location' => 'Location', 'type' => 'Space type', 'acs' => 'ACs', 'on_duration' => 'Known ON duration'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Locations included', 'value' => count($rows)],
                ['label' => 'Total known ON duration', 'value' => $this->hours(array_sum(array_map(fn ($row) => $this->toSeconds($row['on_duration']), $rows)))],
            ],
        ];
    }

    private function stateHistory(Organization $organization, Carbon $from, Carbon $to, array $filters, string $field): array
    {
        $query = DB::table('ac_state_history as h')
            ->join('ac_units as acs', 'acs.id', '=', 'h.ac_unit_id')
            ->join('spaces', 'spaces.id', '=', 'acs.space_id')
            ->join('floors', 'floors.id', '=', 'spaces.floor_id')
            ->join('buildings', 'buildings.id', '=', 'floors.building_id')
            ->join('sites', 'sites.id', '=', 'buildings.site_id')
            ->where('h.organization_id', $organization->id)
            ->where('h.state_field', $field)
            ->whereBetween('h.observed_at', [$from->copy()->utc(), $to->copy()->utc()])
            ->orderByDesc('h.observed_at');

        foreach (['site_id' => 'sites.id', 'building_id' => 'buildings.id', 'floor_id' => 'floors.id', 'space_id' => 'spaces.id', 'ac_id' => 'acs.id'] as $key => $column) {
            if (! empty($filters[$key])) {
                $query->where($column, $filters[$key]);
            }
        }

        $tz = $organization->timezone ?: 'UTC';
        $rows = $query->limit(5000)->get([
            'h.observed_at', 'h.new_value', 'h.source', 'h.quality', 'acs.name', 'acs.asset_code',
            'sites.name as site_name', 'buildings.name as building_name', 'spaces.name as space_name',
        ])->map(function ($row) use ($tz, $field) {
            $value = is_array($row->new_value)
                ? ($row->new_value[$field] ?? $row->new_value['power'] ?? $row->new_value['value'] ?? null)
                : $row->new_value;

            if ($field === 'power_state') {
                $value = match ((string) $value) {
                    'known_on' => 'ON',
                    'known_off' => 'OFF',
                    default => ucfirst((string) $value),
                };
            } elseif (is_numeric($value)) {
                $value = $value.'°C';
            }

            return [
                'time' => Carbon::parse($row->observed_at)->setTimezone($tz)->format('d M Y, H:i'),
                'ac' => $row->asset_code.' — '.$row->name,
                'location' => implode(' › ', array_filter([$row->site_name, $row->building_name, $row->space_name])),
                'value' => $value ?? '—',
                'source' => EventSource::tryFrom($row->source)?->label() ?? ucfirst($row->source),
                'quality' => ucfirst($row->quality),
            ];
        })->all();

        return [
            'title' => $field === 'power_state' ? 'AC ON/OFF History Report' : 'Temperature Setpoint History Report',
            'columns' => ['time' => 'Event time', 'ac' => 'Air conditioner', 'location' => 'Location', 'value' => $field === 'power_state' ? 'State' : 'Setpoint', 'source' => 'Source', 'quality' => 'Quality'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Records', 'value' => count($rows)],
                ['label' => 'Field', 'value' => $field === 'power_state' ? 'Power state' : 'Temperature setpoint'],
            ],
        ];
    }

    private function deviceConnectivity(Organization $organization): array
    {
        $rows = Device::where('organization_id', $organization->id)
            ->with(['currentAssignment.acUnit.space.floor.building.site'])
            ->get()
            ->map(function (Device $device) {
                $ac = $device->acUnit();
                $status = $device->connectivity();

                return [
                    'device' => $device->device_uid,
                    'name' => $device->name,
                    'ac' => $ac?->asset_code ?? '—',
                    'location' => $ac?->locationLabel() ?? '—',
                    'connectivity' => $status->label(),
                    'last_seen' => $device->last_seen_at?->format('d M Y, H:i') ?? 'Never',
                    'policy' => PolicyValue::tryFrom($device->effective_remote_policy)?->label() ?? ucfirst($device->effective_remote_policy),
                    'policy_synced' => $device->policy_synced_at?->format('d M Y, H:i') ?? 'Not synchronized',
                ];
            })->all();

        return [
            'title' => 'Device Connectivity Report',
            'columns' => ['device' => 'Device ID', 'name' => 'Device', 'ac' => 'AC', 'location' => 'Location', 'connectivity' => 'Connectivity', 'last_seen' => 'Last seen', 'policy' => 'Remote policy', 'policy_synced' => 'Policy synced'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Devices', 'value' => count($rows)],
                ['label' => 'Online', 'value' => collect($rows)->where('connectivity', ConnectivityStatus::Online->label())->count()],
                ['label' => 'Offline', 'value' => collect($rows)->where('connectivity', ConnectivityStatus::Offline->label())->count()],
            ],
        ];
    }

    private function scopeLabel(Organization $organization, array $filters): string
    {
        if (! empty($filters['ac_id'])) {
            $ac = AcUnit::where('organization_id', $organization->id)->find($filters['ac_id']);
            if ($ac) {
                return "AC: {$ac->asset_code} — {$ac->name}";
            }
        }

        if (! empty($filters['space_id'])) {
            $ac = AcUnit::where('organization_id', $organization->id)->where('space_id', $filters['space_id'])->first();
            if ($ac?->space) {
                return 'Location: '.$ac->space->fullPath();
            }
        }

        return 'Organization-wide';
    }

    private function csv(array $data): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, [$data['title']]);
        fputcsv($handle, ['Organization', $data['organization']->name]);
        fputcsv($handle, ['Period', $data['period']]);
        fputcsv($handle, ['Scope', $data['scope']]);
        fputcsv($handle, ['Generated', $data['generated_at'].' ('.$data['timezone'].')']);
        fputcsv($handle, []);
        fputcsv($handle, array_values($data['columns']));

        foreach ($data['rows'] as $row) {
            fputcsv($handle, array_map(fn ($column) => $row[$column] ?? '', array_keys($data['columns'])));
        }

        fputcsv($handle, []);
        fputcsv($handle, ['Notes']);
        foreach ($data['notes'] as $note) {
            fputcsv($handle, [$note]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    private function hours(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    private function toSeconds(string $value): int
    {
        if (preg_match('/(\d+)h\s*(\d+)m/', $value, $matches)) {
            return ((int) $matches[1]) * 3600 + ((int) $matches[2]) * 60;
        }

        if (preg_match('/(\d+)m/', $value, $matches)) {
            return ((int) $matches[1]) * 60;
        }

        return 0;
    }
}
