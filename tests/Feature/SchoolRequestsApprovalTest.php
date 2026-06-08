<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\ServiceRequest;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SchoolRequestsApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $schoolAdmin;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->school = School::create([
            'name' => 'Test school',
            'email' => 'school@test.com',
        ]);

        $this->schoolAdmin = User::create([
            'name' => 'School Admin',
            'email' => 'schooladmin@test.com',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_school_admin_can_submit_student_request_and_admin_can_approve_it(): void
    {
        // Pre-create parent user & profile
        $parentUser = User::create([
            'name' => 'Parent Name',
            'email' => 'parent@example.test',
            'password' => bcrypt('password'),
            'role' => 'parent',
        ]);
        $parentProfile = ParentProfile::create([
            'user_id' => $parentUser->id,
            'active' => true,
        ]);

        Sanctum::actingAs($this->schoolAdmin);

        $payload = [
            'request_type' => 'add_student',
            'subject' => 'Add Student: Test Kid',
            'description' => 'Request to add student Test Kid',
            'priority' => 'medium',
            'metadata' => [
                'full_name' => 'Test Kid',
                'grade' => 'Grade 4',
                'parent_id' => $parentProfile->id,
                'bus_id' => null,
                'bus_route_id' => null,
            ],
        ];

        $response = $this->postJson('/api/service-requests', $payload);
        $response->assertStatus(201);

        $this->assertDatabaseHas('service_requests', [
            'subject' => 'Add Student: Test Kid',
            'status' => 'pending',
            'role' => 'school_admin',
        ]);

        $serviceRequest = ServiceRequest::first();

        // Admin resolves/approves the request
        Sanctum::actingAs($this->admin);

        $updateResponse = $this->putJson("/api/admin/service-requests/{$serviceRequest->id}", [
            'status' => 'resolved',
            'admin_response' => 'Approved',
        ]);
        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => 'resolved',
        ]);

        $this->assertDatabaseHas('students', [
            'school_id' => $this->school->id,
            'full_name' => 'Test Kid',
            'grade' => 'Grade 4',
            'parent_id' => $parentProfile->id,
        ]);
    }

    public function test_school_admin_can_submit_bus_request_and_admin_can_approve_it(): void
    {
        Sanctum::actingAs($this->schoolAdmin);

        $payload = [
            'request_type' => 'add_bus',
            'subject' => 'Add Bus: BUS-99',
            'description' => 'Request to add bus BUS-99',
            'priority' => 'medium',
            'metadata' => [
                'bus_number' => 'BUS-99',
                'plate_number' => 'XYZ-1234',
                'capacity' => 40,
            ],
        ];

        $response = $this->postJson('/api/service-requests', $payload);
        $response->assertStatus(201);

        $serviceRequest = ServiceRequest::first();

        // Admin resolves/approves the request
        Sanctum::actingAs($this->admin);

        $updateResponse = $this->putJson("/api/admin/service-requests/{$serviceRequest->id}", [
            'status' => 'resolved',
            'admin_response' => 'Approved',
        ]);
        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('buses', [
            'school_id' => $this->school->id,
            'bus_number' => 'BUS-99',
            'plate_number' => 'XYZ-1234',
            'capacity' => 40,
        ]);
    }

    public function test_school_admin_can_submit_route_request_and_admin_can_approve_it(): void
    {
        Sanctum::actingAs($this->schoolAdmin);

        $payload = [
            'request_type' => 'add_route',
            'subject' => 'Add Route: Smouha Fast',
            'description' => 'Request to add route Smouha Fast',
            'priority' => 'medium',
            'metadata' => [
                'name' => 'Smouha Fast',
                'type' => 'morning',
                'estimated_minutes' => 45,
                'stops' => [
                    ['name' => 'School Gate', 'lat' => 31.2001, 'lng' => 29.9187, 'order' => 1]
                ],
            ],
        ];

        $response = $this->postJson('/api/service-requests', $payload);
        $response->assertStatus(201);

        $serviceRequest = ServiceRequest::first();

        // Admin resolves/approves the request
        Sanctum::actingAs($this->admin);

        $updateResponse = $this->putJson("/api/admin/service-requests/{$serviceRequest->id}", [
            'status' => 'resolved',
            'admin_response' => 'Approved',
        ]);
        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('bus_routes', [
            'school_id' => $this->school->id,
            'name' => 'Smouha Fast',
            'type' => 'morning',
            'estimated_minutes' => 45,
        ]);
    }

    public function test_school_admin_can_submit_trip_request_and_admin_can_approve_it(): void
    {
        // 1. Pre-create driver
        $driverUser = User::create([
            'name' => 'Driver User',
            'email' => 'driver@test.com',
            'password' => Hash::make('password'),
            'role' => 'driver',
        ]);
        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'active' => true,
        ]);

        // 2. Pre-create bus
        $bus = Bus::create([
            'bus_number' => 'BUS-01',
            'capacity' => 30,
            'active' => true,
        ]);

        // 3. Pre-create route
        $route = BusRoute::create([
            'name' => 'Test Route',
            'start_location' => 'School',
            'end_location' => 'Smouha',
        ]);

        Sanctum::actingAs($this->schoolAdmin);

        $payload = [
            'request_type' => 'add_trip',
            'subject' => 'Add Trip: Route X morning',
            'description' => 'Request to add trip',
            'priority' => 'medium',
            'metadata' => [
                'trip_date' => '2026-06-10',
                'shift' => 'morning',
                'driver_id' => $driver->id,
                'bus_id' => $bus->id,
                'bus_route_id' => $route->id,
            ],
        ];

        $response = $this->postJson('/api/service-requests', $payload);
        $response->assertStatus(201);

        $serviceRequest = ServiceRequest::first();

        // Admin resolves/approves the request
        Sanctum::actingAs($this->admin);

        $updateResponse = $this->putJson("/api/admin/service-requests/{$serviceRequest->id}", [
            'status' => 'resolved',
            'admin_response' => 'Approved',
        ]);
        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('trips', [
            'school_id' => $this->school->id,
            'trip_date' => '2026-06-10 00:00:00',
            'shift' => 'morning',
            'driver_id' => $driver->id,
            'bus_id' => $bus->id,
            'bus_route_id' => $route->id,
        ]);
    }
}
