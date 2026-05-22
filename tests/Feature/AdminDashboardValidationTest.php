<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminDashboardValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin SafeStep',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    public function test_add_parent_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        // Expected form inputs mapped in buildRequestFromConfig
        $payload = [
            'name' => 'Parent Name',
            'email' => 'parent@example.test',
            'phone' => '1234567890',
            'student_count' => 2,
            'active' => true,
            'message' => 'Some notes'
        ];

        $response = $this->postJson('/api/admin/parents', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('parents', [
            'phone' => '1234567890',
            'student_count' => 2,
            'message' => 'Some notes'
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Parent Name',
            'email' => 'parent@example.test',
            'role' => 'parent'
        ]);
    }

    public function test_add_driver_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        $payload = [
            'name' => 'Driver Name',
            'email' => 'driver@example.test',
            'phone' => '0987654321',
            'license_number' => 'DL-12345',
            'years_experience' => 5, // extracted from "5 years" in JS
            'active' => true,
            'message' => 'Assigned Bus: Bus #42. Notes: Experience'
        ];

        $response = $this->postJson('/api/admin/drivers', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('drivers', [
            'phone' => '0987654321',
            'license_number' => 'DL-12345',
            'years_experience' => 5,
            'message' => 'Assigned Bus: Bus #42. Notes: Experience'
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Driver Name',
            'email' => 'driver@example.test',
            'role' => 'driver'
        ]);
    }

    public function test_add_bus_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        $payload = [
            'bus_number' => 'B-42',
            'plate_number' => 'PLATE-42',
            'capacity' => 15,
            'active' => true
        ];

        $response = $this->postJson('/api/admin/buses', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('buses', [
            'bus_number' => 'B-42',
            'plate_number' => 'PLATE-42',
            'capacity' => 15,
            'active' => true
        ]);
    }

    public function test_add_student_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

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

        $payload = [
            'full_name' => 'Student Name',
            'parent_id' => $parentProfile->id,
            'grade' => 'Grade 5',
            'school_name' => 'Elementary School',
            'active' => true
        ];

        $response = $this->postJson('/api/admin/students', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('students', [
            'full_name' => 'Student Name',
            'parent_id' => $parentProfile->id,
            'grade' => 'Grade 5',
            'school_name' => 'Elementary School',
            'active' => true
        ]);
    }

    public function test_add_trip_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        // Pre-create driver, bus, and route
        $driverUser = User::create([
            'name' => 'Driver Name',
            'email' => 'driver@example.test',
            'password' => bcrypt('password'),
            'role' => 'driver',
        ]);
        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'active' => true,
        ]);

        $bus = Bus::create([
            'bus_number' => 'B-42',
            'capacity' => 15,
            'active' => true
        ]);

        $route = BusRoute::create([
            'name' => 'Route A',
            'start_location' => 'A',
            'end_location' => 'B'
        ]);

        $payload = [
            'driver_id' => $driver->id,
            'bus_id' => $bus->id,
            'bus_route_id' => $route->id,
            'trip_date' => '2026-05-22',
            'shift' => 'morning',
            'status' => 'assigned'
        ];

        $response = $this->postJson('/api/admin/trips', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('trips', [
            'driver_id' => $driver->id,
            'bus_id' => $bus->id,
            'bus_route_id' => $route->id,
            'trip_date' => '2026-05-22 00:00:00',
            'shift' => 'morning',
            'status' => 'assigned'
        ]);
    }

    public function test_add_user_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        $payload = [
            'name' => 'Staff User',
            'email' => 'staff@example.test',
            'role' => 'parent', // allowed role fallback
            'password' => 'password'
        ];

        $response = $this->postJson('/api/admin/users', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'name' => 'Staff User',
            'email' => 'staff@example.test',
            'role' => 'parent'
        ]);
    }

    public function test_add_school_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        $payload = [
            'name' => 'SafeStep School',
            'address' => '123 Main St',
            'phone' => '123-456-7890',
            'email' => 'school@example.test',
            'notes' => 'District: Central, Type: private, Status: active, Students: 300. Extra notes'
        ];

        $response = $this->postJson('/api/admin/schools', $payload);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('schools', [
            'name' => 'SafeStep School',
            'address' => '123 Main St',
            'phone' => '123-456-7890',
            'email' => 'school@example.test',
            'notes' => 'District: Central, Type: private, Status: active, Students: 300. Extra notes'
        ]);
    }

    public function test_add_financial_entry_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        $payload = [
            'title' => 'Profit Entry',
            'type' => 'income', // mapped from profit
            'amount' => 5000.50,
            'description' => 'Profit from subscriptions',
            'entry_date' => '2026-05-22'
        ];

        $response = $this->postJson('/api/admin/financial-entries', $payload);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('financial_entries', [
            'title' => 'Profit Entry',
            'type' => 'income',
            'amount' => 5000.50,
            'description' => 'Profit from subscriptions',
            'entry_date' => '2026-05-22'
        ]);
    }

    public function test_add_maintenance_record_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        $bus = Bus::create([
            'bus_number' => 'B-42',
            'capacity' => 15,
            'active' => true
        ]);

        $payload = [
            'bus_id' => $bus->id,
            'title' => 'Repair Record',
            'description' => 'Technician: Al. Fixing the engine.',
            'cost' => 350.75,
            'status' => 'pending',
            'maintenance_date' => '2026-05-22'
        ];

        $response = $this->postJson('/api/admin/maintenance-records', $payload);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('maintenance_records', [
            'bus_id' => $bus->id,
            'title' => 'Repair Record',
            'description' => 'Technician: Al. Fixing the engine.',
            'cost' => 350.75,
            'status' => 'pending',
            'maintenance_date' => '2026-05-22'
        ]);
    }

    public function test_add_complaint_validation(): void
    {
        Sanctum::actingAs($this->adminUser);

        $payload = [
            'type' => 'complaint',
            'title' => 'Driver Complaint',
            'body' => "Submitted By: Jane\nPriority: high\nDetails: Driver was speeding.",
            'status' => 'open'
        ];

        $response = $this->postJson('/api/admin/reports', $payload);
        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('reports', [
            'type' => 'complaint',
            'title' => 'Driver Complaint',
            'body' => "Submitted By: Jane\nPriority: high\nDetails: Driver was speeding.",
            'status' => 'open'
        ]);
    }
}
