<?php

namespace App\Services;

use App\Models\Application;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolRegistrationService
{
    public function provisionFromApplication(Application $application): array
    {
        $meta = $application->metadata;
        $admin = User::where('email', $application->email)->first();

        $school = $admin?->school;

        if (! $school) {
            $school = School::create([
                'name' => $meta['school_name'] ?? $application->full_name,
                'email' => $meta['school_email'] ?? $application->email,
                'phone' => $application->phone,
                'address' => $meta['school_address'] ?? $application->address,
                'principal_name' => $meta['principal_name'] ?? $application->full_name,
                'logo' => $meta['school_logo'] ?? null,
                'student_count' => isset($meta['student_count']) ? (int) $meta['student_count'] : null,
                'bus_count' => isset($meta['bus_count']) ? (int) $meta['bus_count'] : null,
                'status' => 'pending_details',
                'active' => false,
                'notes' => 'Provisioned from application #' . $application->id,
            ]);
        }

        $password = Str::password(12);

        $adminData = [
            'name' => $meta['principal_name'] ?? $application->full_name,
            'role' => 'school_admin',
            'school_id' => $school->id,
        ];

        if (! $admin) {
            $adminData['password'] = Hash::make($password);
            $adminData['plain_password'] = $password;
        }

        $admin = User::updateOrCreate(['email' => $application->email], $adminData);

        if ($application->user_id !== $admin->id) {
            $application->update(['user_id' => $admin->id]);
        }

        return [
            'school' => $school,
            'admin' => $admin,
            'temporary_password' => $password,
        ];
    }
}
