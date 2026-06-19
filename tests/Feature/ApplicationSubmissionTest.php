<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function apiHeaders(string $token = null): array
    {
        return [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . ($token ?? config('services.applications.token')),
        ];
    }

    private function validDriverPayload(): array
    {
        return [
            'name'        => 'Test Driver',
            'email'       => 'driver@example.test',
            'password'    => 'password123',
            'password_confirmation' => 'password123',
            'phone'       => '01012345678',
            'address'     => 'Cairo, Egypt',
            'role'        => 'driver',
            'experience'  => 'Five years of safe driving.',
            'notes'       => 'Available immediately.',
            'owner_state' => 'Arab Republic of Egypt',
            'owner_age'   => 30,
            'owner_gender'=> 'Male',
            'car_type'    => 'Toyota HiAce',
            'car_model'   => '2023',
            'car_plate'   => 'ABC 1234',
        ];
    }

    private function validParentPayload(): array
    {
        return [
            'name'                     => 'Test Parent',
            'email'                    => 'parent@example.test',
            'password'                 => 'password123',
            'password_confirmation'    => 'password123',
            'phone'                    => '01098765432',
            'address'                  => 'Giza, Egypt',
            'role'                     => 'parent',
            'experience'               => 'Looking for safe transport for my children.',
            'student_state'            => 'Arab Republic of Egypt',
            'student_relationship'     => 'Father',
            'student_count'            => 2,
            'student_degree'           => 'Primary',
            'student_education_system' => 'National',
            'school_name'              => 'Al-Azhar School',
            'school_address'           => 'Nasr City, Cairo',
            'school_starting'          => '7:30 AM',
        ];
    }

    // ── Test: Authorized request with valid token succeeds ──

    public function test_authorized_request_with_valid_token_succeeds(): void
    {
        $response = $this->postJson(
            '/api/applications',
            $this->validDriverPayload(),
            $this->apiHeaders()
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Application submitted successfully')
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'full_name', 'email', 'role', 'status'],
            ]);
    }

    // ── Test: Unauthorized request without token is rejected ──

    public function test_unauthorized_request_without_token_is_rejected(): void
    {
        $this->postJson('/api/applications', $this->validDriverPayload(), [
            'Accept' => 'application/json',
        ])
            ->assertStatus(401)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Unauthorized request');
    }

    // ── Test: Unauthorized request with wrong token is rejected ──

    public function test_unauthorized_request_with_wrong_token_is_rejected(): void
    {
        $this->postJson(
            '/api/applications',
            $this->validDriverPayload(),
            $this->apiHeaders('wrong-token-value')
        )
            ->assertStatus(401)
            ->assertJsonPath('status', 'error');
    }

    // ── Test: Validation failure returns 422 ──

    public function test_validation_failure_returns_422(): void
    {
        $response = $this->postJson('/api/applications', [
            'role' => 'driver',
            // Missing all required fields
        ], $this->apiHeaders());

        $response
            ->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'errors'])
            ->assertJson(fn ($json) => $json->where('message', fn ($message) => is_string($message) && $message !== '')->etc());
    }

    // ── Test: Valid submission creates database row ──

    public function test_valid_submission_creates_database_row(): void
    {
        $this->postJson(
            '/api/applications',
            $this->validDriverPayload(),
            $this->apiHeaders()
        )->assertStatus(201);

        $this->assertDatabaseHas('applications', [
            'email'  => 'driver@example.test',
            'role'   => 'driver',
            'status' => 'pending',
        ]);
    }

    // ── Test: Role-specific validation works ──

    public function test_role_specific_validation_works(): void
    {
        // Parent submission without required student fields
        $payload = [
            'name'       => 'Test Parent',
            'email'      => 'parent@example.test',
            'phone'      => '01098765432',
            'address'    => 'Cairo',
            'role'       => 'parent',
            'experience' => 'Some experience.',
            // Missing all student_* and school_* fields
        ];

        $response = $this->postJson(
            '/api/applications',
            $payload,
            $this->apiHeaders()
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'student_state',
                'student_relationship',
                'student_count',
            ]);
    }

    // ── Test: Parent submission succeeds with all fields ──

    public function test_parent_submission_with_all_fields_succeeds(): void
    {
        $this->postJson(
            '/api/applications',
            $this->validParentPayload(),
            $this->apiHeaders()
        )
            ->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('applications', [
            'email' => 'parent@example.test',
            'role'  => 'parent',
        ]);
    }

    public function test_application_approval_creates_user_and_profile(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        \Laravel\Sanctum\Sanctum::actingAs($admin);

        // 1. Create a guest driver application (user_id is null)
        $driverApp = \App\Models\Application::create([
            'user_id' => null,
            'full_name' => 'John Driver',
            'email' => 'johndriver@example.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'role' => 'driver',
            'experience' => '3 years experience',
            'notes' => 'meta:{"car_plate":"XYZ 123","car_model":"2022"}',
            'status' => 'pending',
        ]);

        // Act: update status to accepted
        $response = $this->patchJson(
            "/api/admin/applications/{$driverApp->id}/status",
            ['status' => 'accepted']
        );

        // Assert
        $response->assertStatus(200);

        // Verify User was created
        $this->assertDatabaseHas('users', [
            'email' => 'johndriver@example.com',
            'role' => 'driver',
        ]);

        $user = \App\Models\User::where('email', 'johndriver@example.com')->first();
        $this->assertNotNull($user);

        // Verify Application is linked to the User
        $this->assertEquals($user->id, $driverApp->fresh()->user_id);

        // Verify Driver profile was created in pending_details / inactive state
        $this->assertDatabaseHas('drivers', [
            'user_id' => $user->id,
            'car_plate' => 'XYZ 123',
            'car_model' => '2022',
            'status' => 'pending_details',
            'active' => false,
        ]);
    }

    public function test_web_application_approval_creates_user_and_profile(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 1. Create a guest parent application (user_id is null)
        $parentApp = \App\Models\Application::create([
            'user_id' => null,
            'full_name' => 'Jane Parent',
            'email' => 'janeparent@example.com',
            'phone' => '0987654321',
            'address' => 'Web Test Address',
            'role' => 'parent',
            'experience' => 'Parent experience',
            'notes' => 'meta:{"student_state":"Arab Republic of Egypt","student_relationship":"Mother","student_count":"3","student_degree":"Primary","student_education_system":"National","school_name":"Green School","school_address":"Cairo","school_starting":"8:00 AM"}',
            'status' => 'pending',
        ]);

        // Act: update status to accepted via web route
        $response = $this->patch(
            "/admin/applications/{$parentApp->id}/status",
            ['status' => 'accepted'],
            ['Accept' => 'application/json']
        );

        // Assert
        $response->assertStatus(200);

        // Verify User was created
        $this->assertDatabaseHas('users', [
            'email' => 'janeparent@example.com',
            'role' => 'parent',
        ]);

        $user = \App\Models\User::where('email', 'janeparent@example.com')->first();
        $this->assertNotNull($user);

        // Verify Application is linked to the User
        $this->assertEquals($user->id, $parentApp->fresh()->user_id);

        // Verify ParentProfile was created
        $this->assertDatabaseHas('parents', [
            'user_id' => $user->id,
            'relationship' => 'Mother',
            'student_count' => 3,
            'degree' => 'Primary',
            'education_system' => 'National',
            'school_name' => 'Green School',
            'active' => false,
            'status' => 'pending_details',
        ]);
    }

    private function validSchoolPayload(): array
    {
        return [
            'name' => 'Principal Principal',
            'email' => 'principal@school.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '01012345679',
            'address' => 'Alexandria, Egypt',
            'role' => 'school',
            'experience' => 'School fleet management experience.',
            'school_name' => 'Test Academy',
            'school_email' => 'info@testacademy.com',
            'principal_name' => 'Principal Principal',
            'school_address' => 'Smouha, Alexandria',
            'student_count' => 500,
            'bus_count' => 10,
        ];
    }

    public function test_school_submission_succeeds(): void
    {
        $response = $this->postJson(
            '/api/applications',
            $this->validSchoolPayload(),
            $this->apiHeaders()
        );

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('applications', [
            'email' => 'principal@school.test',
            'role' => 'school',
            'status' => 'pending',
        ]);

        $user = \App\Models\User::where('email', 'principal@school.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('school_admin', $user->role);

        $this->assertDatabaseHas('schools', [
            'name' => 'Test Academy',
            'status' => 'pending_details',
        ]);
    }
}
