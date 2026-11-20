<?php

namespace Tests\Feature;

use App\Models\AcUnit;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_another_organizations_ac(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        $userA = $this->makeMember($orgA, 'a@test.test');
        $userB = $this->makeMember($orgB, 'b@test.test');

        $acA = AcUnit::factory()->create(['organization_id' => $orgA->id, 'asset_code' => 'AC-A-01']);
        $acB = AcUnit::factory()->create(['organization_id' => $orgB->id, 'asset_code' => 'AC-B-01']);

        $this->actingAs($userA)
            ->get('/air-conditioners/' . $acA->id)
            ->assertStatus(200);

        $this->actingAs($userA)
            ->get('/air-conditioners/' . $acB->id)
            ->assertStatus(403);

        $this->actingAs($userB)
            ->get('/air-conditioners/' . $acA->id)
            ->assertStatus(403);
    }

    public function test_ac_inventory_is_scoped_to_organization(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        $userA = $this->makeMember($orgA, 'a@test.test');

        AcUnit::factory()->count(3)->create(['organization_id' => $orgA->id]);
        AcUnit::factory()->count(2)->create(['organization_id' => $orgB->id]);

        $response = $this->actingAs($userA)
            ->get('/air-conditioners');

        $response->assertStatus(200);
        $response->assertSeeText('3 air conditioners');
    }

    public function test_user_cannot_issue_command_for_another_organizations_ac(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        $userA = $this->makeMember($orgA, 'a@test.test');

        $acB = AcUnit::factory()->create(['organization_id' => $orgB->id, 'asset_code' => 'AC-B-01']);

        $response = $this->actingAs($userA)
            ->post('/air-conditioners/' . $acB->id . '/commands', [
                'desired_state' => ['power' => 'off'],
            ]);

        $response->assertStatus(403);
    }

    private function makeMember(Organization $organization, string $email): User
    {
        $user = User::factory()->create(['email' => $email, 'password' => bcrypt('password')]);
        $organization->users()->attach($user->id, ['role' => 'organization_admin', 'is_active' => true]);

        return $user->fresh();
    }
}
