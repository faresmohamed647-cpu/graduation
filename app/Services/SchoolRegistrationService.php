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

        $school = School::create([
            'name' => $meta['school_name'] ?? $application->full_name,
            'email' => $meta['school_email'] ?? $application->email,
            'phone' => $application->phone,
            'address' => $meta['school_address'] ?? $application->address,
            'principal_name' => $meta['principal_name'] ?? $application->full_name,
            'logo' => $meta['school_logo'] ?? null,
            'status' => 'pending_details',
            'notes' => 'Provisioned from application #' . $application->id,
        ]);

        $password = Str::password(12);

        $admin = User::updateOrCreate(
            ['email' => $application->email],
            [
                'name' => $meta['principal_name'] ?? $application->full_name,
                'password' => Hash::make($password),
                'plain_password' => $password,
                'role' => 'school_admin',
                'school_id' => $school->id,
            ]
        );

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
