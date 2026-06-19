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

    public function test_profile_is_automatically_created_on_approval(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'parent']);
        
        $application = Application::create([
            'user_id' => $user->id,
            'full_name' => 'Parent Name',
            'email' => 'parent@example.test',
            'phone' => '0123456789',
            'address' => 'Test Address',
            'role' => 'parent',
            'experience' => 'pickup_change',
            'notes' => 'Some notes meta:{"state":"CA","relationship":"father","student_count":2}',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/admin/applications/{$application->id}/status", [
            'status' => 'accepted',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('parents', [
            'user_id' => $user->id,
            'active' => false,
            'status' => 'pending_details',
            'phone' => '0123456789',
            'address' => 'Test Address',
            'state' => 'CA',
            'relationship' => 'father',
            'student_count' => 2,
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

    public function test_parent_dashboard_shows_onboarding_or_waiting_states_correctly(): void
    {
        $parentUser = User::factory()->create(['role' => 'parent']);
        $profile = \App\Models\ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '01012345678',
            'address' => 'Cairo',
            'active' => false,
            'status' => 'pending',
            'student_count' => 1,
        ]);

        $app = Application::create([
            'user_id' => $parentUser->id,
            'full_name' => 'Parent User',
            'email' => $parentUser->email,
            'phone' => '01012345678',
            'address' => 'Cairo',
            'role' => 'parent',
            'experience' => 'Parent registration',
            'status' => 'pending',
        ]);

        // 1. Pending State
        $this->actingAs($parentUser)
            ->get('/parent')
            ->assertOk()
            ->assertSee('Your application is under review', false);

        // 2. Application accepted -> Onboarding form
        $profile->update(['active' => false, 'status' => 'pending_details']);
        $app->update(['status' => 'accepted']);
        $this->actingAs($parentUser)
            ->get('/parent')
            ->assertOk()
            ->assertSee('Register Children Details', false)
            ->assertDontSee('Route and Bus assignment pending');

        // 3. Children submitted, waiting admin approval
        \App\Models\Student::create([
            'parent_id' => $profile->id,
            'full_name' => 'Child One',
            'active' => true,
            'assignment_status' => 'pending',
        ]);
        $profile->update(['status' => 'pending_approval']);
        $profile->refresh();
        $this->actingAs($parentUser)
            ->get('/parent')
            ->assertOk()
            ->assertSee('Children Details Submitted', false);

        // 4. Admin approves parent profile -> dashboard unlocks
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->postJson("/api/admin/parents/{$profile->id}/approve")->assertOk();
        $this->assertDatabaseHas('parents', [
            'id' => $profile->id,
            'status' => 'approved',
            'active' => true,
        ]);

        $this->actingAs($parentUser)
            ->get('/parent')
            ->assertOk()
            ->assertSee('Route and Bus assignment pending', false)
            ->assertDontSee('Children Details Submitted', false)
            ->assertDontSee('Register Children Details', false);

        // 5. Approved + Assigned -> Active
        $bus = \App\Models\Bus::create([
            'bus_number' => 'Bus 99',
            'plate_number' => 'ABC 123',
            'capacity' => 30,
            'active' => true,
        ]);
        $profile->students()->first()->update(['bus_id' => $bus->id, 'assignment_status' => 'assigned']);
        $this->actingAs($parentUser)
            ->get('/parent')
            ->assertOk()
            ->assertSee('Bus Status', false);
    }

    public function test_parent_with_children_and_drifted_status_shows_pending_approval_not_form(): void
    {
        $parentUser = User::factory()->create(['role' => 'parent']);
        $profile = \App\Models\ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '01012345678',
            'address' => 'Cairo',
            'active' => false,
            'status' => 'pending',
            'student_count' => 1,
        ]);

        Application::create([
            'user_id' => $parentUser->id,
            'full_name' => 'Parent User',
            'email' => $parentUser->email,
            'phone' => '01012345678',
            'address' => 'Cairo',
            'role' => 'parent',
            'experience' => 'Parent registration',
            'status' => 'accepted',
        ]);

        \App\Models\Student::create([
            'parent_id' => $profile->id,
            'full_name' => 'Child One',
            'active' => true,
            'assignment_status' => 'pending',
        ]);

        $this->actingAs($parentUser)
            ->get('/parent')
            ->assertOk()
            ->assertSee('Children Details Submitted', false)
            ->assertDontSee('Register Children Details', false);

        $this->assertDatabaseHas('parents', [
            'id' => $profile->id,
            'status' => 'pending_approval',
        ]);
    }

    public function test_driver_dashboard_shows_waiting_states_correctly(): void
    {
        $driverUser = User::factory()->create(['role' => 'driver']);
        $profile = \App\Models\Driver::create([
            'user_id' => $driverUser->id,
            'phone' => '01012345678',
            'license_number' => 'LIC-123',
            'years_experience' => 5,
            'active' => false,
            'status' => 'pending',
        ]);

        $app = Application::create([
            'user_id' => $driverUser->id,
            'full_name' => 'Driver User',
            'email' => $driverUser->email,
            'phone' => '01012345678',
            'address' => 'Cairo',
            'role' => 'driver',
            'experience' => 'Driver registration',
            'status' => 'pending',
        ]);

        // 1. Pending State
        $this->actingAs($driverUser)
            ->get('/driver')
            ->assertOk()
            ->assertSee('طلب التقديم قيد المراجعة', false);

        // 2. Approved but no Bus assigned
        $profile->update(['active' => true, 'status' => 'approved']);
        $app->update(['status' => 'accepted']);
        $driverUser->refresh();
        $this->actingAs($driverUser)
            ->get('/driver')
            ->assertOk()
            ->assertSee('بانتظار تعيين حافلة وخط سير', false)
            ->assertDontSee('Ready to Start');

        // 3. Bus assigned -> Active
        \App\Models\Bus::create([
            'driver_id' => $profile->id,
            'bus_number' => 'Bus 42',
            'plate_number' => 'XYZ 999',
            'capacity' => 45,
            'active' => true,
        ]);
        $driverUser->refresh();
        $this->actingAs($driverUser)
            ->get('/driver')
            ->assertOk()
            ->assertSee('Ready to Start', false);
    }

    public function test_parent_can_submit_children_details(): void
    {
        $parentUser = User::factory()->create(['role' => 'parent']);
        $profile = \App\Models\ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '01012345678',
            'address' => 'Cairo',
            'active' => false,
            'status' => 'pending_details',
            'student_count' => 1,
            'school_name' => 'Test School',
        ]);

        Sanctum::actingAs($parentUser);

        $payload = [
            'children' => [
                [
                    'full_name' => 'Child Name One',
                    'age' => 8,
                    'grade' => 'Grade 3',
                    'school_name' => 'Test School',
                    'pickup_location' => 'Home Address',
                    'dropoff_location' => 'School Address',
                    'pickup_time' => '07:30',
                    'dropoff_time' => '14:30',
                    'has_medical_condition' => false,
                ]
            ]
        ];

        $response = $this->postJson('/api/parent/children/submit', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('students', [
            'parent_id' => $profile->id,
            'full_name' => 'Child Name One',
        ]);
        $this->assertDatabaseHas('parents', [
            'id' => $profile->id,
            'status' => 'pending_approval',
            'active' => false,
        ]);
    }

    public function test_driver_can_submit_profile_details_and_admin_can_approve(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $driverUser = User::factory()->create(['role' => 'driver']);
        $profile = \App\Models\Driver::create([
            'user_id' => $driverUser->id,
            'phone' => '01012345678',
            'license_number' => 'LIC-123',
            'years_experience' => 5,
            'active' => false,
            'status' => 'pending_details',
        ]);

        Sanctum::actingAs($driverUser);

        $nationalId = \Illuminate\Http\UploadedFile::fake()->image('national_id.jpg');
        $criminalRecord = \Illuminate\Http\UploadedFile::fake()->create('criminal_record.pdf', 100);

        $payload = [
            'full_name' => 'John Doe Driver',
            'phone' => '01211112222',
            'age' => 35,
            'gender' => 'male',
            'license_number' => 'LIC-999',
            'years_experience' => 10,
            'car_type' => 'Mini Bus',
            'car_model' => 'Coaster 2024',
            'car_plate' => 'ABC-1234',
            'address' => 'Alexandria, Egypt',
            'national_id' => $nationalId,
            'criminal_record' => $criminalRecord,
        ];

        // Submit onboarding details
        $response = $this->postJson('/api/driver/details/submit', $payload);
        $response->assertOk();

        $this->assertDatabaseHas('drivers', [
            'user_id' => $driverUser->id,
            'status' => 'pending_approval',
            'full_name' => 'John Doe Driver',
            'car_plate' => 'ABC-1234',
            'active' => false,
        ]);

        $freshProfile = $profile->fresh();
        $this->assertNotNull($freshProfile->national_id_path);
        $this->assertNotNull($freshProfile->criminal_record_path);

        // Admin approves driver
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $approveResponse = $this->postJson("/api/admin/drivers/{$profile->id}/approve");
        $approveResponse->assertOk();

        $this->assertDatabaseHas('drivers', [
            'id' => $profile->id,
            'status' => 'approved',
            'active' => true,
        ]);
    }

    public function test_school_can_submit_profile_details_and_admin_can_approve(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $schoolAdmin = User::factory()->create(['role' => 'school_admin']);
        $school = \App\Models\School::create([
            'name' => 'Pending School',
            'status' => 'pending_details',
            'active' => false,
        ]);
        $schoolAdmin->update(['school_id' => $school->id]);

        Sanctum::actingAs($schoolAdmin);

        $licenseDoc = \Illuminate\Http\UploadedFile::fake()->create('school_license.pdf', 150);
        $insuranceDoc = \Illuminate\Http\UploadedFile::fake()->create('school_insurance.pdf', 150);

        $payload = [
            'name' => 'Approved School Name',
            'principal_name' => 'Principal Ahmed',
            'email' => 'principal@school.test',
            'phone' => '0123456789',
            'address' => 'Cairo, Egypt',
            'student_count' => 250,
            'bus_count' => 5,
            'operating_hours_start' => '07:30',
            'operating_hours_end' => '14:30',
            'commercial_register' => 'CR-998877',
            'license_number' => 'SCHOOL-LIC-123',
            'license_expiry' => '2027-06-30',
            'license_document' => $licenseDoc,
            'insurance_document' => $insuranceDoc,
            'fleet_type' => 'own',
            'notes' => 'Test school profile submission.',
        ];

        // Submit school details
        $response = $this->postJson('/api/school-admin/details/submit', $payload);
        $response->assertOk();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'status' => 'pending_approval',
            'name' => 'Approved School Name',
            'fleet_type' => 'own',
            'student_count' => 250,
            'bus_count' => 5,
            'active' => false,
        ]);

        $freshSchool = $school->fresh();
        $this->assertNotNull($freshSchool->license_document_path);
        $this->assertNotNull($freshSchool->insurance_document_path);
        $this->assertNotNull($freshSchool->profile_submitted_at);

        // Admin approves school
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $approveResponse = $this->postJson("/api/admin/schools/{$school->id}/approve");
        $approveResponse->assertOk();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'status' => 'active',
            'active' => true,
        ]);
    }
}
