<?php

namespace Database\Factories;

use App\Models\AcModel;
use App\Models\IrProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IrProfile>
 */
class IrProfileFactory extends Factory
{
    protected $model = IrProfile::class;

    public function definition(): array
    {
        return [
            'ac_model_id' => AcModel::factory(),
            'name' => fake()->word() . ' IR Profile',
            'status' => 'published',
        ];
    }
}
