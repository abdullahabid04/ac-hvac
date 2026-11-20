<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in');
    }

    public function test_login_authenticates_and_redirects_to_overview(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'email' => 'admin@northwind.test',
            'password' => bcrypt('password'),
        ]);
        $organization->users()->attach($user->id, ['role' => 'organization_admin', 'is_active' => true]);

        $response = $this->post('/login', [
            'email' => 'admin@northwind.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/overview');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'admin@northwind.test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@northwind.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_authenticated_routes_require_login(): void
    {
        $response = $this->get('/overview');

        $response->assertRedirect('/login');
    }
}
