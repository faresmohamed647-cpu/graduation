<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SanctumRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_creates_sanctum_token(): void
    {
        User::create([
            'name' => 'Admin SafeStep',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'admin@example.test',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.roles.0', 'admin')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user',
                ],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_admin_routes_require_token_and_correct_role(): void
    {
        $this->getJson('/api/admin/dashboard/stats')
            ->assertStatus(401)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Unauthenticated');

        Sanctum::actingAs($this->userWithRole('parent'));

        $this->getJson('/api/admin/dashboard/stats')
            ->assertStatus(403);

        Sanctum::actingAs($this->userWithRole('admin'));

        $this->getJson('/api/admin/dashboard/stats')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_school_admin_routes_require_school_admin_role(): void
    {
        $school = School::create([
            'name' => 'Test School',
            'status' => 'active',
        ]);

        $schoolAdmin = User::create([
            'name' => 'School Admin',
            'email' => 'schooladmin@example.test',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
            'school_id' => $school->id,
        ]);

        Sanctum::actingAs($this->userWithRole('parent'));
        $this->getJson('/api/school-admin/dashboard/stats')->assertStatus(403);

        Sanctum::actingAs($schoolAdmin);
        $this->getJson('/api/school-admin/dashboard/stats')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_driver_and_parent_routes_accept_correct_roles(): void
    {
        $driverUser = $this->userWithRole('driver');
        Driver::create([
            'user_id' => $driverUser->id,
            'phone' => '01000000000',
            'license_number' => 'TEST-DRIVER',
            'years_experience' => 3,
            'active' => true,
        ]);

        Sanctum::actingAs($driverUser);
        $this->getJson('/api/driver/dashboard')->assertOk();

        $parentUser = $this->userWithRole('parent');
        ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '01100000000',
            'address' => 'Test address',
            'active' => true,
        ]);

        Sanctum::actingAs($parentUser);
        $this->getJson('/api/parent/children')->assertOk();
    }

    private function userWithRole(string $role): User
    {
        return User::create([
            'name' => ucfirst($role).' User',
            'email' => $role.'@example.test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }
}
