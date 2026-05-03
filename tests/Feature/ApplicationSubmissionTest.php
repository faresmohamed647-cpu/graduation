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
            ->assertJsonPath('message', 'Validation failed')
            ->assertJsonStructure(['status', 'message', 'errors']);
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
}
