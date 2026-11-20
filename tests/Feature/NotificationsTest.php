<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Organization;
use App\Models\User;
use App\Enums\NotificationSeverity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_list_is_tenant_scoped(): void
    {
        $userA = $this->createUser('Org A', 'a@test.test');
        $userB = $this->createUser('Org B', 'b@test.test');

        Notification::factory()->count(3)->create([
            'organization_id' => $userA->currentOrganization()->id,
            'user_id' => $userA->id,
            'severity' => NotificationSeverity::Info,
        ]);
        Notification::factory()->count(2)->create([
            'organization_id' => $userB->currentOrganization()->id,
            'user_id' => $userB->id,
            'severity' => NotificationSeverity::Info,
        ]);

        $response = $this->actingAs($userA)
            ->get('/notifications');

        $response->assertStatus(200);
        $response->assertSee('3 total');
    }

    public function test_mark_notification_read(): void
    {
        $user = $this->createUser('Org N', 'n@test.test');
        $notification = Notification::factory()->create([
            'organization_id' => $user->currentOrganization()->id,
            'user_id' => $user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->post('/notifications/' . $notification->id . '/read');

        $response->assertStatus(302);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_all_read(): void
    {
        $user = $this->createUser('Org M', 'm@test.test');
        Notification::factory()->count(3)->create([
            'organization_id' => $user->currentOrganization()->id,
            'user_id' => $user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->post('/notifications/read-all');

        $response->assertStatus(302);
        $this->assertCount(0, $user->fresh()->notifications()->whereNull('read_at')->get());
    }

    public function test_cannot_mark_another_organizations_notification_read(): void
    {
        $userA = $this->createUser('Org A', 'a@test.test');
        $userB = $this->createUser('Org B', 'b@test.test');

        $notification = Notification::factory()->create([
            'organization_id' => $userB->currentOrganization()->id,
            'user_id' => $userB->id,
        ]);

        $response = $this->actingAs($userA)
            ->post('/notifications/' . $notification->id . '/read');

        $response->assertStatus(404);
    }

    private function createUser(string $orgName, string $email): User
    {
        $organization = Organization::factory()->create(['name' => $orgName]);
        $user = User::factory()->create(['email' => $email, 'password' => bcrypt('password')]);
        $organization->users()->attach($user->id, ['role' => 'organization_admin', 'is_active' => true]);

        return $user;
    }
}
