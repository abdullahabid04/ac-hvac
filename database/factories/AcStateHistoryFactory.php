<?php

namespace Database\Factories;

use App\Models\AcStateHistory;
use App\Models\AcUnit;
use App\Models\Organization;
use App\Enums\PowerState;
use App\Enums\StateQuality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcStateHistory>
 */
class AcStateHistoryFactory extends Factory
{
    protected $model = AcStateHistory::class;

    public function definition(): array
    {
        $ac = AcUnit::factory()->create();
        $organization = $ac->organization;
        $occurredAt = fake()->dateTimeBetween('-14 days', 'now');
        $power = fake()->randomElement([PowerState::KnownOn, PowerState::KnownOff, PowerState::Unknown]);

        return [
            'organization_id' => $organization->id,
            'ac_unit_id' => $ac->id,
            'device_event_id' => null,
            'command_id' => null,
            'source' => 'device',
            'state_field' => 'power_state',
            'old_value' => ['power' => $power === PowerState::KnownOn ? 'off' : 'on'],
            'new_value' => ['power' => $power->value],
            'observed_at' => $occurredAt,
            'received_at' => now()->parse($occurredAt)->addSeconds(2),
            'quality' => StateQuality::Reported,
        ];
    }
}
