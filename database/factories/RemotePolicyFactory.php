<?php

namespace Database\Factories;

use App\Models\RemotePolicy;
use App\Models\Organization;
use App\Enums\PolicyValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RemotePolicy>
 */
class RemotePolicyFactory extends Factory
{
    protected $model = RemotePolicy::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory()->create()->id,
            'scope_type' => 'organization',
            'scope_id' => 0,
            'policy_value' => PolicyValue::Disabled,
            'version' => 1,
            'updated_by' => null,
        ];
    }
}
