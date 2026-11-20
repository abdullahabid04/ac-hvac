<?php

namespace Database\Factories;

use App\Models\Floor;
use App\Models\Organization;
use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Space>
 */
class SpaceFactory extends Factory
{
    protected $model = Space::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory()->create()->id,
            'floor_id' => Floor::factory()->create()->id,
            'parent_space_id' => null,
            'name' => fake()->randomElement(['Room 101', 'Room 102', 'Room 201', 'Room 202', 'Room 301', 'Conference Room', 'Open Office', 'Server Room', 'Break Room', 'Training Room', 'Executive Suite', 'Reception']),
            'code' => null,
            'space_type' => fake()->randomElement(['room', 'corridor', 'hall', 'gallery', 'section', 'other']),
            'status' => 'active',
            'area_sqm' => fake()->randomElement([12.5, 18.0, 24.0, 32.5, 45.0, 60.0, 75.0]),
        ];
    }
}
