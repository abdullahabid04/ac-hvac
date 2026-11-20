<?php

namespace Tests\Feature;

use App\Models\GeneratedReport;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_pdf_report(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->post('/reports', [
                'report_type' => 'daily_usage',
                'format' => 'pdf',
                'from' => now()->subDays(6)->toDateString(),
                'to' => now()->toDateString(),
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $report = GeneratedReport::first();
        $this->assertEquals('completed', $report->status);
        $this->assertNotNull($report->file_path);
        $this->assertTrue(Storage::disk('local')->exists($report->file_path));
    }

    public function test_can_generate_csv_report(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->post('/reports', [
                'report_type' => 'ac_history',
                'format' => 'csv',
                'from' => now()->subDays(30)->toDateString(),
                'to' => now()->toDateString(),
            ]);

        $response->assertStatus(302);
        $this->assertEquals('completed', GeneratedReport::first()->status);
    }

    public function test_can_download_completed_report(): void
    {
        $user = $this->createUser();
        $report = GeneratedReport::factory()->create([
            'organization_id' => $user->currentOrganization()->id,
            'status' => 'completed',
            'format' => 'csv',
            'file_path' => 'reports/test.csv',
        ]);
        Storage::disk('local')->put('reports/test.csv', 'a,b' . "\n" . '1,2');

        $response = $this->actingAs($user)
            ->get('/reports/' . $report->id . '/download');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('a,b', Storage::disk('local')->get($report->file_path));
    }

    public function test_cannot_download_another_organizations_report(): void
    {
        $userA = $this->createUser();
        $orgB = \App\Models\Organization::factory()->create();
        $userB = User::factory()->create();
        $orgB->users()->attach($userB->id, ['role' => 'organization_admin', 'is_active' => true]);

        $report = GeneratedReport::factory()->create([
            'organization_id' => $userA->currentOrganization()->id,
            'status' => 'completed',
            'file_path' => 'reports/secret.pdf',
        ]);
        Storage::disk('local')->put('reports/secret.pdf', 'secret');

        $response = $this->actingAs($userB)
            ->get('/reports/' . $report->id . '/download');

        $response->assertStatus(404);
    }

    private function createUser(): User
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $organization->users()->attach($user->id, ['role' => 'organization_admin', 'is_active' => true]);

        return $user;
    }
}
