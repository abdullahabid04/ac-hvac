<?php

namespace Database\Factories;

use App\Models\AcUnit;
use App\Models\Space;
use App\Models\AcModel;
use App\Models\IrProfile;
use App\Models\IrProfileVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcUnit>
 */
class AcUnitFactory extends Factory
{
    protected $model = AcUnit::class;

    public function definition(): array
    {
        $space = Space::factory()->create();
        $model = AcModel::factory()->create();
        $profile = IrProfile::factory()->create(['ac_model_id' => $model->id]);
        $profileVersion = IrProfileVersion::factory()->create([
            'ir_profile_id' => $profile->id,
            'validation_status' => fake()->randomElement(['validated', 'validated', 'unvalidated']),
        ]);

        return [
            'organization_id' => $space->organization_id,
            'space_id' => $space->id,
            'ac_model_id' => $model->id,
            'ir_profile_version_id' => $profileVersion->id,
            'name' => fake()->randomElement(['Main Office AC', 'Conference Room AC', 'Server Room AC', 'Reception AC', 'Open Plan AC', 'Executive Office AC']),
            'asset_code' => Str::upper(fake()->bothify('AC-###-##')),
            'serial_number' => Str::upper(fake()->bothify('SN-########')),
            'installation_date' => fake()->dateTimeBetween('-3 years', 'today')->format('Y-m-d'),
            'status' => fake()->randomElement(['active', 'active', 'active', 'maintenance', 'inactive']),
            'notes' => 'Seeded demonstration unit.',
        ];
    }
}
