<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\AcUnit;
use App\Models\Organization;
use App\Models\User;
use App\Models\Device;
use App\Enums\EventSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        $organization = Organization::factory()->create();
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id]);
        $source = fake()->randomElement([EventSource::Portal, EventSource::PhysicalRemote, EventSource::Device, EventSource::System]);
        $result = fake()->randomElement(['success', 'success', 'success', 'pending', 'failure']);
        $occurredAt = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'organization_id' => $organization->id,
            'ac_unit_id' => $ac->id,
            'source' => $source,
            'action' => fake()->randomElement(['ac_registered', 'ac_updated', 'command_issued', 'command_completed', 'state_reported', 'remote_action', 'policy_updated', 'device_offline', 'device_reconnected']),
            'description' => fake()->sentence(6),
            'result' => $result,
            'user_id' => User::factory()->create(['name' => 'Demo Admin'])->id,
            'device_id' => Device::factory()->create(['organization_id' => $organization->id])->id,
            'command_id' => null,
            'device_event_id' => null,
            'occurred_at' => $occurredAt,
            'metadata' => [],
        ];
    }
}
