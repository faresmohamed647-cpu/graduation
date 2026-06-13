<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SchoolAdminDashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_admin_dashboard_endpoints_return_success(): void
    {
        $school = School::create([
            'name' => 'Active Test School',
            'email' => 'school@test.com',
            'status' => 'active',
            'active' => true,
        ]);

        $admin = User::factory()->create([
            'role' => 'school_admin',
            'school_id' => $school->id,
        ]);

        Sanctum::actingAs($admin);

        $endpoints = [
            '/api/school-admin/dashboard/stats',
            '/api/school-admin/dashboard/attendance-summary',
            '/api/school-admin/dashboard/trips-overview?days=7',
            '/api/school-admin/dashboard/fleet-status',
            '/api/school-admin/dashboard/attendance-trends?days=30',
            '/api/school-admin/dashboard/safety-reports?months=6',
            '/api/school-admin/dashboard/kpis',
            '/api/school-admin/notifications',
        ];

        foreach ($endpoints as $endpoint) {
            $this->getJson($endpoint)
                ->assertOk()
                ->assertJsonPath('success', true);
        }
    }

    public function test_active_school_admin_web_dashboard_loads_without_validation_error(): void
    {
        $school = School::create([
            'name' => 'Web Test School',
            'email' => 'web-school@test.com',
            'status' => 'active',
            'active' => true,
        ]);

        $admin = User::factory()->create([
            'role' => 'school_admin',
            'school_id' => $school->id,
        ]);

        $this->actingAs($admin)
            ->get('/school-admin')
            ->assertOk()
            ->assertSee('School Dashboard', false)
            ->assertSee('isApproved', false);
    }

    public function test_new_school_admin_sees_onboarding_form(): void
    {
        $school = School::create([
            'name' => 'Pending School',
            'email' => 'pending-school@test.com',
            'status' => 'pending_details',
            'active' => false,
        ]);

        $admin = User::factory()->create([
            'role' => 'school_admin',
            'school_id' => $school->id,
        ]);

        $this->actingAs($admin)
            ->get('/school-admin')
            ->assertOk()
            ->assertSee('Complete School Profile', false)
            ->assertSee('id="schoolOnboardingForm"', false);
    }

    public function test_school_admin_login_redirects_to_dashboard(): void
    {
        $school = School::create([
            'name' => 'Login School',
            'email' => 'login-school@test.com',
            'status' => 'pending_details',
            'active' => false,
        ]);

        $admin = User::factory()->create([
            'role' => 'school_admin',
            'school_id' => $school->id,
        ]);

        $this->postJson('/login', [
            'email' => $admin->email,
            'password' => 'password',
            'role' => 'school_admin',
        ])
            ->assertOk()
            ->assertJsonPath('redirect', '/school-admin');
    }
}
