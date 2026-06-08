<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SchoolApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_application_can_be_accepted_and_provisions_school(): void
    {
        $admin = User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $application = Application::create([
            'full_name' => 'Principal Test',
            'email' => 'principal@test.com',
            'phone' => '01000000001',
            'address' => 'Alexandria',
            'role' => 'school',
            'experience' => 'School fleet management experience.',
            'notes' => 'meta:' . json_encode([
                'school_name' => 'Test Academy',
                'school_email' => 'info@testacademy.com',
                'principal_name' => 'Principal Test',
                'school_address' => 'Smouha, Alexandria',
                'student_count' => 500,
                'bus_count' => 10,
            ]),
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/admin/applications/{$application->id}/status", ['status' => 'accepted'])
            ->assertOk();

        $this->assertDatabaseHas('schools', ['name' => 'Test Academy']);
        $this->assertDatabaseHas('users', [
            'email' => 'principal@test.com',
            'role' => 'school_admin',
        ]);

        $school = School::where('name', 'Test Academy')->first();
        $this->assertNotNull($school);
        $this->assertDatabaseHas('users', ['school_id' => $school->id, 'role' => 'school_admin']);
    }
}
