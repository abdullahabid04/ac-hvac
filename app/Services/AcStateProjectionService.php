<?php

namespace App\Services;

use App\Enums\PowerState;
use App\Enums\StateQuality;
use App\Models\AcCommand;
use App\Models\AcStateHistory;
use App\Models\AcStateSnapshot;
use App\Models\AcUnit;
use App\Models\DeviceEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AcStateProjectionService
{
    public function __construct(private UsageAggregationService $usage) {}

    /**
     * Apply a recognized state report/command inference to the current snapshot.
     * Out-of-order observations never overwrite newer state.
     */
    public function applyConfiguration(
        AcUnit $ac,
        array $state,
        string $source,
        StateQuality $quality,
        Carbon $observedAt,
        Carbon $receivedAt,
        ?DeviceEvent $deviceEvent = null,
        ?AcCommand $command = null,
    ): bool {
        return DB::transaction(function () use ($ac, $state, $source, $quality, $observedAt, $receivedAt, $deviceEvent, $command) {
            $snapshot = AcStateSnapshot::firstOrNew(['ac_unit_id' => $ac->id]);

            if ($snapshot->exists && $snapshot->observed_at && $snapshot->observed_at->gt($observedAt)) {
                return false;
            }

            if (! $snapshot->exists) {
                $snapshot->organization_id = $ac->organization_id;
                $snapshot->power_state = PowerState::Unknown;
                $snapshot->quality = StateQuality::Unknown;
            }

            $changes = [];

            if (array_key_exists('power', $state)) {
                $rawPower = $state['power'];
                if (is_string($rawPower)) {
                    $rawPower = match ($rawPower) {
                        'on' => 'known_on',
                        'off' => 'known_off',
                        default => $rawPower,
                    };
                }
                $newPower = $rawPower instanceof PowerState
                    ? $rawPower
                    : PowerState::from($rawPower);

                if ($snapshot->power_state !== $newPower) {
                    $changes['power_state'] = [$snapshot->power_state?->value, $newPower->value, ['power' => $newPower->value]];
                    $snapshot->power_state = $newPower;
                }
            }

            foreach (['temperature_setpoint_c', 'operating_mode', 'fan_setting', 'swing_setting'] as $field) {
                if (! array_key_exists($field, $state)) {
                    continue;
                }

                $value = $state[$field];

                if ($value === '') {
                    $value = null;
                }

                if ($snapshot->{$field} !== $value) {
                    $changes[$field] = [$snapshot->{$field}, $value, [$field => $value]];
                    $snapshot->{$field} = $value;
                }
            }

            $snapshot->fill([
                'organization_id' => $ac->organization_id,
                'source' => $source,
                'observed_at' => $observedAt,
                'received_at' => $receivedAt,
                'quality' => $quality,
                'ir_profile_version_id' => $ac->ir_profile_version_id,
            ])->save();

            foreach ($changes as $field => [$old, $new, $newValue]) {
                AcStateHistory::create([
                    'organization_id' => $ac->organization_id,
                    'ac_unit_id' => $ac->id,
                    'device_event_id' => $deviceEvent?->id,
                    'command_id' => $command?->id,
                    'source' => $source,
                    'state_field' => $field,
                    'old_value' => $old === null ? null : [$field === 'power_state' ? 'power' : $field => $old],
                    'new_value' => $newValue,
                    'observed_at' => $observedAt,
                    'received_at' => $receivedAt,
                    'quality' => $quality,
                ]);
            }

            $this->usage->rebuildForAc($ac, $observedAt);

            return $changes !== [];
        });
    }

    public function applyPowerState(
        AcUnit $ac,
        PowerState $power,
        string $source,
        StateQuality $quality,
        Carbon $observedAt,
        Carbon $receivedAt,
        ?DeviceEvent $deviceEvent = null,
        ?AcCommand $command = null,
    ): bool {
        return $this->applyConfiguration(
            $ac,
            ['power' => $power],
            $source,
            $quality,
            $observedAt,
            $receivedAt,
            $deviceEvent,
            $command,
        );
    }
}
