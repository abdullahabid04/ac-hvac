<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Building>
 */
class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory()->create()->id,
            'site_id' => Site::factory()->create()->id,
            'name' => fake()->randomElement(['Building A', 'Building B', 'North Wing', 'South Wing', 'Corporate Tower', 'Warehouse 1', 'Warehouse 2', 'Admin Block']),
            'code' => Str::upper(fake()->bothify('BLD-??-##')),
            'status' => 'active',
        ];
    }
}
