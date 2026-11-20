<?php

namespace Database\Factories;

use App\Models\AcModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcModel>
 */
class AcModelFactory extends Factory
{
    protected $model = AcModel::class;

    public function definition(): array
    {
        $manufacturer = fake()->randomElement(['Daikin', 'Mitsubishi', 'LG', 'Samsung', 'Carrier', 'Panasonic', 'Hitachi', 'Gree']);
        $model = fake()->randomElement(['Wall Mount', 'Cassette', 'Ducted', 'Floor Standing', 'Ceiling Suspended']);

        return [
            'manufacturer' => $manufacturer,
            'model_name' => $manufacturer . ' ' . $model . ' ' . fake()->numerify('##00'),
            'remote_variant' => fake()->randomElement(['standard', 'inverter', 'premium', 'compact']),
            'protocol_name' => 'NEC-IR',
            'notes' => 'Catalogue reference model.',
        ];
    }
}
