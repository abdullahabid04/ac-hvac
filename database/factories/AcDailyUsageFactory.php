<?php

namespace Database\Factories;

use App\Models\AcDailyUsage;
use App\Models\AcUnit;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcDailyUsage>
 */
class AcDailyUsageFactory extends Factory
{
    protected $model = AcDailyUsage::class;

    public function definition(): array
    {
        $ac = AcUnit::factory()->create();
        $organization = $ac->organization;
        $date = fake()->dateTimeBetween('-14 days', 'now');

        return [
            'organization_id' => $organization->id,
            'ac_unit_id' => $ac->id,
            'usage_date' => $date,
            'on_seconds' => fake()->randomElement([0, 3600, 7200, 10800, 14400, 21600, 28800]),
            'unknown_seconds' => fake()->randomElement([0, 0, 0, 1800, 3600]),
            'incomplete' => fake()->boolean(15),
            'calculated_at' => now(),
        ];
    }
}
