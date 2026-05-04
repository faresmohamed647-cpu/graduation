<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApplicationController extends Controller
{
    public function store(ApplicationRequest $request)
    {
        try {
            $data = $request->validated();
            $role = strtolower($data['role']);

            $roleMetadata = match ($role) {
                ApplicationRole::Parent->value => [
                    'student_state' => $data['student_state'] ?? null,
                    'student_relationship' => $data['student_relationship'] ?? null,
                    'student_count' => $data['student_count'] ?? null,
                    'student_degree' => $data['student_degree'] ?? null,
                    'student_education_system' => $data['student_education_system'] ?? null,
                    'school_name' => $data['school_name'] ?? null,
                    'school_address' => $data['school_address'] ?? null,
                    'school_starting' => $data['school_starting'] ?? null,
                ],
                ApplicationRole::Driver->value => [
                    'owner_state' => $data['owner_state'] ?? null,
                    'owner_age' => $data['owner_age'] ?? null,
                    'owner_gender' => $data['owner_gender'] ?? null,
                    'car_type' => $data['car_type'] ?? null,
                    'car_model' => $data['car_model'] ?? null,
                    'car_plate' => $data['car_plate'] ?? null,
                ],
                ApplicationRole::Admin->value => [
                    'admin_department' => $data['admin_department'] ?? null,
                    'years_experience' => $data['years_experience'] ?? null,
                    'highest_qualification' => $data['highest_qualification'] ?? null,
                    'availability' => $data['availability'] ?? null,
                ],
                default => [],
            };

            $notes = trim((string) ($data['notes'] ?? ''));
            if ($roleMetadata !== []) {
                $notes = trim($notes . ($notes !== '' ? PHP_EOL : '') . 'meta:' . json_encode($roleMetadata, JSON_UNESCAPED_UNICODE));
            }

            // Try to link application to authenticated user if available
            $userId = null;
            try {
                $userId = $request->user()?->id;
            } catch (\Throwable $e) {
                // Not authenticated, that's fine for public applications
            }

            // Also try to match by email if no auth user
            if (!$userId) {
                $matchedUser = \App\Models\User::where('email', $data['email'])->first();
                $userId = $matchedUser?->id;
            }

            $application = Application::create([
                'user_id' => $userId,
                'full_name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'role' => $role,
                'experience' => $data['experience'],
                'notes' => $notes !== '' ? $notes : null,
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Application submitted successfully',
                'data' => $application,
            ], 201);
        } catch (Throwable $exception) {
            Log::error('Failed to store application', [
                'ip' => $request->ip(),
                'role' => $request->input('role'),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to submit application',
                'errors' => ['server' => ['Please try again later.']],
            ], 500);
        }
    }
}
