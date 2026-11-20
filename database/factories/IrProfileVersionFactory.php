<?php

namespace Database\Factories;

use App\Models\IrProfile;
use App\Models\IrProfileVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IrProfileVersion>
 */
class IrProfileVersionFactory extends Factory
{
    protected $model = IrProfileVersion::class;

    public function definition(): array
    {
        $min = fake()->randomElement([16, 18]);
        $max = fake()->randomElement([30, 32]);
        $step = fake()->randomElement([0.5, 1]);

        return [
            'ir_profile_id' => IrProfile::factory(),
            'version_number' => fake()->randomElement([1, 2, 3]),
            'protocol_metadata' => ['carrier_hz' => 38000, 'encoding' => 'NEC'],
            'capabilities' => [
                'power' => true,
                'setpoint' => true,
                'mode' => true,
                'fan' => true,
                'swing' => true,
            ],
            'temperature_min_c' => $min,
            'temperature_max_c' => $max,
            'temperature_step_c' => $step,
            'validation_status' => fake()->randomElement(['validated', 'validated', 'validated', 'unvalidated']),
            'validation_notes' => 'Factory-validated reference profile.',
            'validated_at' => now(),
        ];
    }
}
