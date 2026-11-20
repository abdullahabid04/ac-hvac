<?php

namespace Database\Factories;

use App\Models\AcStateSnapshot;
use App\Models\AcUnit;
use App\Models\Organization;
use App\Enums\PowerState;
use App\Enums\StateQuality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcStateSnapshot>
 */
class AcStateSnapshotFactory extends Factory
{
    protected $model = AcStateSnapshot::class;

    public function definition(): array
    {
        $ac = AcUnit::factory()->create();
        $organization = $ac->organization;
        $power = fake()->randomElement([PowerState::KnownOn, PowerState::KnownOn, PowerState::KnownOff, PowerState::Unknown, PowerState::Stale]);
        $stale = $power === PowerState::Stale || fake()->boolean(20);
        $observedAt = $stale ? now()->subHours(fake()->numberBetween(3, 48)) : now()->subMinutes(fake()->numberBetween(1, 120));

        return [
            'organization_id' => $organization->id,
            'ac_unit_id' => $ac->id,
            'power_state' => $power,
            'temperature_setpoint_c' => $power === PowerState::KnownOn || $power === PowerState::KnownOff ? fake()->numberBetween(18, 26) : null,
            'operating_mode' => 'cool',
            'fan_setting' => 'auto',
            'swing_setting' => 'auto',
            'source' => 'device',
            'observed_at' => $observedAt,
            'received_at' => now()->parse($observedAt)->addSeconds(2),
            'quality' => $stale ? StateQuality::Stale : StateQuality::Reported,
            'ir_profile_version_id' => $ac->ir_profile_version_id,
        ];
    }
}
