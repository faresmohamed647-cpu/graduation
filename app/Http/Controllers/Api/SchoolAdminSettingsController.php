<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SchoolAdminSettingsController extends Controller
{
    use ResolvesSchoolScope;

    public function show(Request $request)
    {
        $school = $this->school($request);

        return response()->json([
            'success' => true,
            'data' => [
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'email' => $school->email,
                    'phone' => $school->phone,
                    'address' => $school->address,
                    'principal_name' => $school->principal_name,
                    'logo' => $school->logo ? asset('storage/' . $school->logo) : null,
                    'status' => $school->status,
                ],
                'admin' => [
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ],
            ],
        ]);
    }

    public function updateSchool(Request $request, ActivityLogService $logger)
    {
        $school = $this->school($request);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        $school->update($data);
        $logger->log($request, 'school.updated', $school);

        return response()->json(['success' => true, 'data' => $school, 'message' => 'School profile updated']);
    }

    public function updateProfile(Request $request, ActivityLogService $logger)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['plain_password'] = $request->input('password');
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $logger->log($request, 'profile.updated', $user);

        return response()->json(['success' => true, 'message' => 'Profile updated']);
    }

    public function activityLogs(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $logs = ActivityLog::where('school_id', $schoolId)
            ->with('user:id,name')
            ->latest()
            ->paginate((int) $request->get('per_page', 50));

        return response()->json(['success' => true, 'data' => $logs->items(), 'meta' => [
            'current_page' => $logs->currentPage(),
            'total' => $logs->total(),
        ]]);
    }
}
