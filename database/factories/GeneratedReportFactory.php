<?php

namespace Database\Factories;

use App\Models\GeneratedReport;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GeneratedReport>
 */
class GeneratedReportFactory extends Factory
{
    protected $model = GeneratedReport::class;

    public function definition(): array
    {
        $organization = Organization::factory()->create();

        return [
            'id' => (string) Str::uuid(),
            'organization_id' => $organization->id,
            'requested_by' => User::factory()->create()->id,
            'report_type' => fake()->randomElement(['daily_usage', 'weekly_usage', 'monthly_usage', 'location_usage', 'ac_history', 'setpoint_history', 'device_connectivity']),
            'title' => 'Operational Report',
            'filters' => ['from' => now()->subDays(7)->toDateString(), 'to' => now()->toDateString()],
            'format' => fake()->randomElement(['pdf', 'csv']),
            'status' => fake()->randomElement(['completed', 'completed', 'completed', 'failed']),
            'file_path' => null,
            'error_message' => null,
            'completed_at' => now(),
            'expires_at' => now()->addDays(30),
        ];
    }
}
