<?php

namespace Database\Factories;

use App\Models\DeviceEvent;
use App\Models\Device;
use App\Models\Organization;
use App\Enums\EventSource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceEvent>
 */
class DeviceEventFactory extends Factory
{
    protected $model = DeviceEvent::class;

    public function definition(): array
    {
        $device = Device::factory()->create();
        $organization = $device->organization;
        $eventType = fake()->randomElement(['state.report', 'heartbeat', 'remote.signal', 'policy.ack']);
        $occurredAt = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'id' => (string) Str::uuid(),
            'event_uid' => (string) Str::uuid(),
            'organization_id' => $organization->id,
            'device_id' => $device->id,
            'ac_unit_id' => $device->acUnit()?->id,
            'command_id' => null,
            'event_type' => $eventType,
            'source' => EventSource::Device,
            'classification' => $eventType === 'remote.signal' ? 'physical_remote' : 'telemetry',
            'device_occurred_at' => $occurredAt,
            'server_received_at' => now()->parse($occurredAt)->addSeconds(fake()->numberBetween(1, 5)),
            'schema_version' => 1,
            'payload' => [
                'power' => fake()->randomElement(['on', 'off']),
                'temperature_setpoint_c' => fake()->numberBetween(18, 26),
                'simulated' => true,
            ],
            'processing_status' => 'processed',
        ];
    }
}
