<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' ' . fake()->randomElement(['Facilities', 'Properties', 'Group', 'Holdings']),
            'slug' => Str::slug(fake()->unique()->company()) . '-' . fake()->numerify('##'),
            'timezone' => fake()->randomElement(['Asia/Karachi', 'Asia/Kolkata', 'Asia/Dubai', 'Asia/Bangkok', 'UTC', 'Europe/London']),
            'status' => 'active',
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'settings' => [],
        ];
    }
}
