<?php

namespace Tests\Feature;

use App\Models\AcDailyUsage;
use App\Models\AcUnit;
use App\Models\Organization;
use App\Models\User;
use App\Enums\PowerState;
use App\Services\UsageAggregationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_page_renders(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->get('/analytics');

        $response->assertStatus(200);
        $response->assertSee('Usage & Analytics');
    }

    public function test_daily_usage_aggregate_is_stored(): void
    {
        $user = $this->createUser();
        $ac = AcUnit::factory()->create(['organization_id' => $user->currentOrganization()->id]);

        AcDailyUsage::create([
            'organization_id' => $user->currentOrganization()->id,
            'ac_unit_id' => $ac->id,
            'usage_date' => now()->toDateString(),
            'on_seconds' => 7200,
            'unknown_seconds' => 0,
            'incomplete' => false,
            'calculated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('/analytics');

        $response->assertStatus(200);
    }

    private function createUser(): User
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $organization->users()->attach($user->id, ['role' => 'organization_admin', 'is_active' => true]);

        return $user;
    }
}
