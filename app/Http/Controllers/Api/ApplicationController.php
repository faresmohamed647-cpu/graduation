<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\School;
use App\Models\User;
use App\Services\AdminSubmissionNotifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ApplicationController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $role = $request->input('role');

        $query = Application::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email', $user->email);
            })
            ->latest();

        if ($role) {
            $query->where(DB::raw('LOWER(role)'), strtolower($role));
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->limit(50)->get(),
        ]);
    }

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
                ApplicationRole::School->value => [
                    'school_name' => $data['school_name'] ?? null,
                    'school_email' => $data['school_email'] ?? null,
                    'principal_name' => $data['principal_name'] ?? null,
                    'school_address' => $data['school_address'] ?? null,
                    'student_count' => $data['student_count'] ?? null,
                    'bus_count' => $data['bus_count'] ?? null,
                    'school_logo' => $this->storeSchoolLogo($request),
                ],
                default => [],
            };

            $notes = trim((string) ($data['notes'] ?? ''));
            if ($roleMetadata !== []) {
                $notes = trim($notes . ($notes !== '' ? PHP_EOL : '') . 'meta:' . json_encode($roleMetadata, JSON_UNESCAPED_UNICODE));
            }

            if ($role === ApplicationRole::School->value) {
                $application = $this->storeSchoolApplication($data, $roleMetadata, $notes);
            } else {
                $matchedUser = $this->resolveApplicationUser($request, $data, $role);

                $application = Application::create([
                    'user_id' => $matchedUser?->id,
                    'full_name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'role' => $role,
                    'experience' => $data['experience'],
                    'notes' => $notes !== '' ? $notes : null,
                    'status' => 'pending',
                ]);
            }

            AdminSubmissionNotifier::notify(
                'application',
                'New application',
                ucfirst($role) . ": {$data['name']}",
                ['id' => $application->id, 'role' => $role, 'action' => 'applications']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Application submitted successfully',
                'data' => $application,
            ], 201);
        } catch (ValidationException $exception) {
            throw $exception;
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

    private function storeSchoolLogo(ApplicationRequest $request): ?string
    {
        if (! $request->hasFile('school_logo')) {
            return null;
        }

        return $request->file('school_logo')->store('applications/school-logos', 'public');
    }

    private function storeSchoolApplication(array $data, array $metadata, string $notes): Application
    {
        return DB::transaction(function () use ($data, $metadata, $notes) {
            $existingUser = User::where('email', $data['email'])->first();

            if ($existingUser && ! in_array($existingUser->role, ['school_admin', 'school'], true)) {
                throw ValidationException::withMessages([
                    'email' => ['This email is already registered with another account type.'],
                ]);
            }

            $school = $existingUser?->school;

            if (! $school) {
                $school = School::create([
                    'name' => $metadata['school_name'] ?? $data['name'],
                    'email' => $metadata['school_email'] ?? $data['email'],
                    'phone' => $data['phone'],
                    'address' => $metadata['school_address'] ?? $data['address'],
                    'principal_name' => $metadata['principal_name'] ?? $data['name'],
                    'logo' => $metadata['school_logo'] ?? null,
                    'student_count' => isset($metadata['student_count']) ? (int) $metadata['student_count'] : null,
                    'bus_count' => isset($metadata['bus_count']) ? (int) $metadata['bus_count'] : null,
                    'status' => 'pending_details',
                    'active' => false,
                    'notes' => 'Provisioned from school registration.',
                ]);
            } elseif ($school->status !== 'active') {
                $school->fill([
                    'name' => $metadata['school_name'] ?? $school->name,
                    'email' => $metadata['school_email'] ?? $school->email,
                    'phone' => $data['phone'] ?? $school->phone,
                    'address' => $metadata['school_address'] ?? $school->address,
                    'principal_name' => $metadata['principal_name'] ?? $school->principal_name,
                    'student_count' => isset($metadata['student_count']) ? (int) $metadata['student_count'] : $school->student_count,
                    'bus_count' => isset($metadata['bus_count']) ? (int) $metadata['bus_count'] : $school->bus_count,
                ]);

                if (! empty($metadata['school_logo'])) {
                    $school->logo = $metadata['school_logo'];
                }

                if (! in_array($school->status, ['pending_details', 'pending_approval'], true)) {
                    $school->status = 'pending_details';
                    $school->active = false;
                }

                $school->save();
            }

            $userData = [
                'name' => $data['name'],
                'role' => 'school_admin',
                'school_id' => $school->id,
            ];

            if (! empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
                $userData['plain_password'] = $data['password'];
            }

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $userData
            );

            return Application::create([
                'user_id' => $user->id,
                'full_name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'role' => ApplicationRole::School->value,
                'experience' => $data['experience'],
                'notes' => $notes !== '' ? $notes : null,
                'status' => 'accepted',
            ]);
        });
    }

    private function resolveApplicationUser(ApplicationRequest $request, array $data, string $role): ?User
    {
        $email = strtolower((string) $data['email']);
        $authUser = $request->user();

        if (
            $authUser
            && strtolower((string) $authUser->email) === $email
            && strtolower((string) $authUser->role) === $role
        ) {
            return $authUser;
        }

        if ($role === ApplicationRole::School->value) {
            return User::where('email', $data['email'])->first();
        }

        $matchedUser = User::where('email', $data['email'])->first();

        if ($matchedUser) {
            if (strtolower((string) $matchedUser->role) !== $role && $matchedUser->role !== 'admin') {
                $matchedUser->update(['role' => $role]);
            }

            return $matchedUser;
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'plain_password' => $data['password'],
            'role' => $role,
        ]);
    }
}
