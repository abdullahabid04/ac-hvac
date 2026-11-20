<?php

namespace Tests\Feature;

use App\Models\AcUnit;
use App\Models\Organization;
use App\Models\RemotePolicy;
use App\Models\Space;
use App\Models\User;
use App\Enums\PolicyScope;
use App\Enums\PolicyValue;
use App\Services\RemotePolicyResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_remote_policy_is_disabled(): void
    {
        $organization = Organization::factory()->create();
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id]);

        $resolver = app(RemotePolicyResolver::class);
        $effective = $resolver->effective($ac);

        $this->assertEquals(PolicyValue::Disabled, $effective['value']);
        $this->assertSame('default', $effective['source_scope']);
    }

    public function test_organization_policy_overrides_default(): void
    {
        $organization = Organization::factory()->create();
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id]);

        app(RemotePolicyResolver::class)->setPolicy(
            $organization,
            PolicyScope::Organization,
            $organization->id,
            PolicyValue::Enabled,
        );

        $effective = app(RemotePolicyResolver::class)->effective($ac);
        $this->assertEquals(PolicyValue::Enabled, $effective['value']);
        $this->assertSame('organization', $effective['source_scope']);
    }

    public function test_ac_override_wins_over_organization_policy(): void
    {
        $organization = Organization::factory()->create();
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id]);

        app(RemotePolicyResolver::class)->setPolicy(
            $organization,
            PolicyScope::Organization,
            $organization->id,
            PolicyValue::Enabled,
        );
        app(RemotePolicyResolver::class)->setPolicy(
            $organization,
            PolicyScope::Ac,
            $ac->id,
            PolicyValue::Disabled,
        );

        $effective = app(RemotePolicyResolver::class)->effective($ac);
        $this->assertEquals(PolicyValue::Disabled, $effective['value']);
        $this->assertSame('ac', $effective['source_scope']);
    }

    public function test_space_policy_inherits_from_parent_chain(): void
    {
        $organization = Organization::factory()->create();
        $space = Space::factory()->create(['organization_id' => $organization->id]);
        $ac = AcUnit::factory()->create(['organization_id' => $organization->id, 'space_id' => $space->id]);

        // No policy anywhere -> default disabled
        $effective = app(RemotePolicyResolver::class)->effective($ac);
        $this->assertEquals(PolicyValue::Disabled, $effective['value']);

        // Set organization to enabled -> AC inherits enabled
        app(RemotePolicyResolver::class)->setPolicy(
            $organization,
            PolicyScope::Organization,
            $organization->id,
            PolicyValue::Enabled,
        );
        $effective = app(RemotePolicyResolver::class)->effective($ac);
        $this->assertEquals(PolicyValue::Enabled, $effective['value']);
    }

    public function test_settings_policy_update_persists(): void
    {
        $user = $this->createUser('Org P', 'p@test.test');
        $organization = $user->currentOrganization();

        $this->actingAs($user)
            ->put('/settings/policy', [
                'scope_type' => 'organization',
                'scope_id' => $organization->id,
                'policy_value' => 'enabled',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('remote_policies', [
            'organization_id' => $organization->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'policy_value' => 'enabled',
        ]);
    }

    private function createUser(string $orgName = 'Org', string $email = 'a@test.test'): User
    {
        $organization = Organization::factory()->create(['name' => $orgName]);
        $user = User::factory()->create(['email' => $email, 'password' => bcrypt('password')]);
        $organization->users()->attach($user->id, ['role' => 'organization_admin', 'is_active' => true]);

        return $user;
    }
}
