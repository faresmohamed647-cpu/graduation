<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApplicationRuntimeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_applications_api_returns_only_authenticated_users_records(): void
    {
        $parent = User::factory()->create(['role' => 'parent', 'email' => 'parent@example.test']);
        $other = User::factory()->create(['role' => 'parent', 'email' => 'other@example.test']);

        Application::create([
            'user_id' => $parent->id,
            'full_name' => 'Parent Record',
            'email' => $parent->email,
            'phone' => '01000000000',
            'address' => 'Cairo',
            'role' => 'parent',
            'experience' => 'pickup_change',
            'status' => 'pending',
        ]);

        Application::create([
            'user_id' => $other->id,
            'full_name' => 'Other Record',
            'email' => $other->email,
            'phone' => '01000000001',
            'address' => 'Giza',
            'role' => 'parent',
            'experience' => 'absence',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($parent);

        $response = $this->getJson('/api/applications?role=parent');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'parent@example.test');
    }

    public function test_admin_can_update_application_status_via_api_without_web_reload(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $application = Application::create([
            'user_id' => $admin->id,
            'full_name' => 'Driver Record',
            'email' => 'driver@example.test',
            'phone' => '01000000002',
            'address' => 'Alexandria',
            'role' => 'driver',
            'experience' => 'maintenance',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/admin/applications/{$application->id}/status", [
            'status' => 'accepted',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.status', 'accepted');

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'accepted',
        ]);
    }

    public function test_admin_applications_route_uses_original_admin_dashboard_section(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/applications')
            ->assertOk()
            ->assertSee('THIS IS THE REAL ADMIN DASHBOARD', false)
            ->assertSee('data-page="applications"', false)
            ->assertSee('id="applications"', false)
            ->assertSee('applicationsTable', false);
    }
}
