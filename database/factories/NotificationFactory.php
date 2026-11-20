<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\AcUnit;
use App\Models\Organization;
use App\Models\User;
use App\Models\Device;
use App\Enums\NotificationSeverity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $severity = fake()->randomElement([NotificationSeverity::Info, NotificationSeverity::Info, NotificationSeverity::Warning, NotificationSeverity::Alert]);
        $occurredAt = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'type' => fake()->randomElement(['device_offline', 'device_reconnected', 'command_failure', 'stale_state', 'physical_remote', 'unknown_ir', 'command_completed']),
            'severity' => $severity,
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(8),
            'ac_unit_id' => AcUnit::factory()->create(['organization_id' => $organization->id])->id,
            'device_id' => Device::factory()->create(['organization_id' => $organization->id])->id,
            'device_event_id' => null,
            'read_at' => fake()->boolean(40) ? now() : null,
            'action_url' => null,
            'occurred_at' => $occurredAt,
        ];
    }
}
