<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory()->create()->id,
            'name' => fake()->randomElement(['GreenTech HQ', 'North Campus', 'West Plaza', 'City Tower', 'Industrial Zone', 'Building A', 'Building B', 'Regional Office']),
            'code' => Str::upper(fake()->bothify('SITE-??-##')),
            'address' => fake()->address(),
            'timezone' => 'Asia/Karachi',
            'status' => 'active',
        ];
    }
}
