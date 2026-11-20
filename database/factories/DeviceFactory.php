<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        $online = fake()->randomElement([true, true, true, false]);
        $lastSeen = $online ? now()->subMinutes(fake()->numberBetween(1, 240)) : now()->subHours(fake()->numberBetween(6, 72));

        return [
            'organization_id' => Organization::factory()->create()->id,
            'device_uid' => Str::upper(fake()->bothify('ESP32-########')),
            'name' => fake()->randomElement(['ESP32-Office', 'ESP32-Conference', 'ESP32-Server', 'ESP32-Reception', 'ESP32-OpenPlan']),
            'hardware_model' => 'ESP32-WROOM-32',
            'firmware_version' => fake()->randomElement(['1.0.0', '1.1.2', '1.2.0', '2.0.1']),
            'status' => 'active',
            'api_token_hash' => null,
            'token_prefix' => Str::upper(fake()->bothify('dev-##')),
            'last_seen_at' => $lastSeen,
            'offline_notified_at' => null,
            'last_ip' => fake()->ipv4(),
            'wifi_rssi_dbm' => fake()->numberBetween(-70, -40),
            'effective_remote_policy' => 'disabled',
            'policy_version' => 1,
            'policy_synced_at' => null,
            'local_config' => [],
        ];
    }
}
