<?php

namespace Database\Factories;

use App\Models\DeviceAssignment;
use App\Models\Device;
use App\Models\AcUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceAssignment>
 */
class DeviceAssignmentFactory extends Factory
{
    protected $model = DeviceAssignment::class;

    public function definition(): array
    {
        $device = Device::factory()->create();
        $ac = AcUnit::factory()->create(['organization_id' => $device->organization_id]);

        return [
            'organization_id' => $device->organization_id,
            'device_id' => $device->id,
            'ac_unit_id' => $ac->id,
            'assigned_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'unassigned_at' => null,
            'is_current' => true,
            'assigned_by' => null,
        ];
    }
}
