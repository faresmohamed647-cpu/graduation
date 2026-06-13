<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Driver;
use App\Models\ParentProfile;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\SchoolRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminApplicationController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Application::latest()->limit(200)->get(),
        ]);
    }

    public function updateStatus(Request $request, Application $application, SchoolRegistrationService $schoolRegistration, ActivityLogService $logger)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'accepted', 'rejected', 'needs_info'])],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['status'] === 'needs_info' && ! empty($data['admin_note'])) {
            $application->update([
                'status' => 'needs_info',
                'notes' => trim(($application->clean_notes ?? '') . "\nAdmin: " . $data['admin_note']),
            ]);
            $logger->log($request, 'application.needs_info', $application);

            return response()->json(['status' => 'success', 'data' => $application->fresh()]);
        }

        $application->update(['status' => $data['status']]);

        if (in_array($data['status'], ['accepted', 'rejected'], true)) {
            $role = strtolower((string) $application->role);
            $active = $data['status'] === 'accepted';
            $meta = $application->metadata;

            if ($role === 'school' && $active) {
                $provisioned = $schoolRegistration->provisionFromApplication($application);
                $logger->log($request, 'application.school_accepted', $application, [
                    'school_id' => $provisioned['school']->id,
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => $application->fresh(),
                    'provisioned' => [
                        'school_id' => $provisioned['school']->id,
                        'admin_email' => $provisioned['admin']->email,
                    ],
                ]);
            }

            $user = $this->resolveApplicationUser($application, $role);

            if ($active && ! $user) {
                $user = User::create([
                    'name' => $application->full_name,
                    'email' => $application->email,
                    'password' => Hash::make('password123'),
                    'plain_password' => 'password123',
                    'role' => $role === 'school' ? 'school_admin' : $role,
                ]);
            }

            if ($user) {
                if ($user->role === 'admin' && $role !== 'admin') {
                    throw ValidationException::withMessages([
                        'application' => ['This application is linked to an admin account and cannot change it to ' . $role . '.'],
                    ]);
                }

                if ($active) {
                    $user->update(['role' => $role === 'school' ? 'school_admin' : $role]);
                }
                if ($application->user_id !== $user->id) {
                    $application->update(['user_id' => $user->id]);
                }

                if ($role === 'parent') {
                    ParentProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        array_filter([
                            'active' => $active,
                            'phone' => $application->phone ?: ($meta['phone'] ?? null),
                            'address' => $application->address ?: ($meta['address'] ?? null),
                            'state' => $meta['student_state'] ?? ($meta['state'] ?? null),
                            'relationship' => $meta['student_relationship'] ?? ($meta['relationship'] ?? null),
                            'student_count' => $meta['student_count'] ?? null,
                            'degree' => $meta['student_degree'] ?? ($meta['degree'] ?? null),
                            'education_system' => $meta['student_education_system'] ?? ($meta['education_system'] ?? null),
                            'school_name' => $meta['school_name'] ?? null,
                            'school_address' => $meta['school_address'] ?? null,
                            'school_starting' => $meta['school_starting'] ?? null,
                            'message' => $meta['message'] ?? $application->clean_notes,
                        ], fn ($v) => $v !== null)
                    );
                } elseif ($role === 'driver') {
                    Driver::updateOrCreate(
                        ['user_id' => $user->id],
                        array_filter([
                            'active' => false,
                            'status' => $active ? 'pending_details' : 'rejected',
                            'full_name' => $application->full_name ?: ($meta['full_name'] ?? null),
                            'phone' => $application->phone ?: ($meta['phone'] ?? null),
                            'address' => $application->address ?: ($meta['address'] ?? null),
                            'license_number' => $meta['license_number'] ?? null,
                            'years_experience' => isset($meta['years_experience']) ? (int) $meta['years_experience'] : null,
                            'age' => $meta['owner_age'] ?? ($meta['age'] ?? null),
                            'gender' => $meta['owner_gender'] ?? ($meta['gender'] ?? null),
                            'car_type' => $meta['car_type'] ?? null,
                            'car_model' => $meta['car_model'] ?? null,
                            'car_plate' => $meta['car_plate'] ?? null,
                            'state' => $meta['owner_state'] ?? ($meta['state'] ?? null),
                            'message' => $meta['message'] ?? $application->clean_notes,
                        ], fn ($v) => $v !== null)
                    );
                }
            }

            $logger->log($request, 'application.' . $data['status'], $application);
        }

        return response()->json([
            'status' => 'success',
            'data' => $application->fresh(),
        ]);
    }

    private function resolveApplicationUser(Application $application, string $role): ?User
    {
        if ($application->user_id) {
            $linkedUser = User::find($application->user_id);
            if ($linkedUser) {
                $linkedEmail = strtolower((string) $linkedUser->email);
                $linkedRole = strtolower((string) $linkedUser->role);
                $email = strtolower((string) $application->email);

                if ($linkedEmail === $email || ($linkedRole === $role && $linkedRole !== 'admin')) {
                    return $linkedUser;
                }
            }
        }

        return User::where('email', $application->email)->first();
    }
}
