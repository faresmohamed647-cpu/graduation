<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Driver;
use App\Models\ParentProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminApplicationController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Application::latest()->limit(100)->get(),
        ]);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'accepted', 'rejected'])],
        ]);

        $application->update(['status' => $data['status']]);

        if (in_array($data['status'], ['accepted', 'rejected'], true)) {
            $role = strtolower((string) $application->role);
            $active = $data['status'] === 'accepted';
            $meta = $application->metadata; // parsed JSON from notes column

            $user = null;
            if ($application->user_id) {
                $user = \App\Models\User::find($application->user_id);
            }
            if (!$user) {
                $user = \App\Models\User::where('email', $application->email)->first();
            }

            if ($active && !$user) {
                $user = \App\Models\User::create([
                    'name' => $application->full_name,
                    'email' => $application->email,
                    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                    'plain_password' => 'password123',
                    'role' => $role,
                ]);
            }

            if ($user) {
                if ($active) {
                    $user->update(['role' => $role]);
                }
                if ($application->user_id !== $user->id) {
                    $application->update(['user_id' => $user->id]);
                }

                if ($role === 'parent') {
                    ParentProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        array_filter([
                            'active'           => $active,
                            'phone'            => $application->phone ?: ($meta['phone'] ?? null),
                            'address'          => $application->address ?: ($meta['address'] ?? null),
                            'state'            => $meta['student_state'] ?? ($meta['state'] ?? null),
                            'relationship'     => $meta['student_relationship'] ?? ($meta['relationship'] ?? null),
                            'student_count'    => $meta['student_count'] ?? null,
                            'degree'           => $meta['student_degree'] ?? ($meta['degree'] ?? null),
                            'education_system' => $meta['student_education_system'] ?? ($meta['education_system'] ?? null),
                            'school_name'      => $meta['school_name'] ?? null,
                            'school_address'   => $meta['school_address'] ?? null,
                            'school_starting'  => $meta['school_starting'] ?? null,
                            'message'          => $meta['message'] ?? $application->clean_notes,
                        ], fn ($v) => $v !== null)
                    );
                } elseif ($role === 'driver') {
                    Driver::updateOrCreate(
                        ['user_id' => $user->id],
                        array_filter([
                            'active'           => $active,
                            'status'           => $active ? 'approved' : 'rejected',
                            'full_name'        => $application->full_name ?: ($meta['full_name'] ?? null),
                            'phone'            => $application->phone ?: ($meta['phone'] ?? null),
                            'address'          => $application->address ?: ($meta['address'] ?? null),
                            'license_number'   => $meta['license_number'] ?? null,
                            'years_experience' => isset($meta['years_experience']) ? (int) $meta['years_experience'] : null,
                            'age'              => $meta['owner_age'] ?? ($meta['age'] ?? null),
                            'gender'           => $meta['owner_gender'] ?? ($meta['gender'] ?? null),
                            'car_type'         => $meta['car_type'] ?? null,
                            'car_model'        => $meta['car_model'] ?? null,
                            'car_plate'        => $meta['car_plate'] ?? null,
                            'state'            => $meta['owner_state'] ?? ($meta['state'] ?? null),
                            'message'          => $meta['message'] ?? $application->clean_notes,
                        ], fn ($v) => $v !== null)
                    );
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $application->fresh(),
        ]);
    }
}
