<?php

namespace App\Services;

use App\Enums\ApplicationRole;
use App\Models\Application;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolLoginProvisioner
{
    public function provisionFromExistingApplication(string $email, string $password): ?User
    {
        $normalizedEmail = strtolower($email);
        $application = Application::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->where('role', ApplicationRole::School->value)
            ->where('status', '!=', 'rejected')
            ->latest()
            ->first();

        if (! $application) {
            $application = Application::query()
                ->where('role', ApplicationRole::School->value)
                ->where('status', '!=', 'rejected')
                ->latest()
                ->get()
                ->first(function (Application $application) use ($normalizedEmail) {
                    return strtolower((string) ($application->metadata['school_email'] ?? '')) === $normalizedEmail;
                });
        }

        if (! $application) {
            return null;
        }

        return DB::transaction(function () use ($application, $email, $password) {
            $meta = $application->metadata;
            $loginEmail = strtolower($email);
            $applicationEmail = strtolower((string) $application->email);
            $schoolEmail = strtolower((string) ($meta['school_email'] ?? ''));
            $userEmail = $loginEmail === $schoolEmail ? $email : $application->email;
            $user = User::where('email', $userEmail)->first()
                ?: User::where('email', $application->email)->first();

            if ($user && ! in_array($user->role, ['school_admin', 'school'], true)) {
                return null;
            }

            $school = $user?->school;

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
                    'notes' => 'Provisioned during school login from application #' . $application->id,
                ]);
            }

            $userData = [
                'name' => $meta['principal_name'] ?? $application->full_name,
                'password' => Hash::make($password),
                'plain_password' => $password,
                'role' => 'school_admin',
                'school_id' => $school->id,
            ];

            if ($user) {
                $user->update($userData);
            } else {
                $user = User::create([
                    'email' => $userEmail,
                    ...$userData,
                ]);
            }

            if ($application->user_id !== $user->id || $application->status === 'pending') {
                $application->update([
                    'user_id' => $user->id,
                    'status' => 'accepted',
                ]);
            }

            return $user;
        });
    }

    public function repairPasswordFromPlainText(User $user, string $password): bool
    {
        if (! $user->plain_password || ! hash_equals((string) $user->plain_password, $password)) {
            return false;
        }

        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        return true;
    }
}
