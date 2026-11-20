<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Floor>
 */
class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory()->create()->id,
            'building_id' => Building::factory()->create()->id,
            'name' => fake()->randomElement(['Ground Floor', 'Floor 1', 'Floor 2', 'Floor 3', 'Floor 4', 'Floor 5', 'Mezzanine', 'Roof']),
            'floor_number' => fake()->randomElement([0, 1, 2, 3, 4, 5]),
            'status' => 'active',
        ];
    }
}
