<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ParentApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if (!$parentId) {
            return response()->json(['success' => true, 'data' => ['children' => [], 'latest_trip' => null]]);
        }
        $children = Student::where('parent_id', $parentId)->where('active', true)->get()
            ->map(fn ($s) => ['id' => $s->id, 'full_name' => $s->full_name, 'grade' => $s->grade, 'school_name' => $s->school_name]);
        $latestTrip = Trip::whereHas('attendance', fn ($q) => $q->whereIn('student_id', $children->pluck('id')))
            ->latest('id')->first();
        return response()->json(['success' => true, 'data' => ['children' => $children, 'latest_trip' => $latestTrip]]);
    }

    public function children(Request $request)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if (!$parentId) { return response()->json(['success' => true, 'data' => []]); }
        $students = Student::where('parent_id', $parentId)->get();
        return response()->json(['success' => true, 'data' => $students]);
    }

    public function child(Request $request, Student $student)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if ($student->parent_id !== $parentId) { abort(403); }
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function childAttendance(Request $request, Student $student)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if ($student->parent_id !== $parentId) { abort(403); }
        $attendance = $student->attendance()->with('trip')->latest()->limit(30)->get();
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function notifications(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()->notifications()->latest()->limit(30)->get()]);
    }

    public function markNotificationRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->whereKey($id)->firstOrFail();
        $n->markAsRead();
        return response()->json(['success' => true, 'message' => 'Marked as read']);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All marked as read']);
    }

    /**
     * Get parent's applications/requests from the database.
     */
    public function childTripHistory(Request $request, Student $student)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if ($student->parent_id !== $parentId) {
            abort(403);
        }

        $history = $student->attendance()
            ->with(['trip.bus', 'trip.route', 'trip.driver.user'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($a) => [
                'trip_date' => $a->trip?->trip_date,
                'bus' => $a->trip?->bus?->bus_number,
                'route' => $a->trip?->route?->name,
                'driver' => $a->trip?->driver?->user?->name,
                'status' => $a->status,
                'picked_up_at' => $a->picked_up_at,
                'dropped_off_at' => $a->dropped_off_at,
            ]);

        return response()->json(['success' => true, 'data' => $history]);
    }

    public function attendanceReport(Request $request)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if (! $parentId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $studentIds = Student::where('parent_id', $parentId)->pluck('id');
        $from = $request->get('from', now()->subDays(30)->toDateString());

        $summary = \App\Models\Attendance::query()
            ->selectRaw('students.full_name as student, attendance.status, COUNT(*) as count')
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->join('trips', 'attendance.trip_id', '=', 'trips.id')
            ->whereIn('attendance.student_id', $studentIds)
            ->where('trips.trip_date', '>=', $from)
            ->groupBy('students.full_name', 'attendance.status')
            ->get();

        return response()->json(['success' => true, 'data' => $summary]);
    }

    public function emergencyStatus(Request $request)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if (! $parentId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $schoolIds = Student::where('parent_id', $parentId)->pluck('school_id')->filter()->unique();
        $studentIds = Student::where('parent_id', $parentId)->pluck('id');

        $alerts = \App\Models\EmergencyAlert::where(function ($q) use ($schoolIds, $studentIds) {
            $q->whereIn('school_id', $schoolIds)->orWhereIn('student_id', $studentIds);
        })->latest()->limit(20)->get();

        return response()->json(['success' => true, 'data' => $alerts]);
    }

    public function requests(Request $request)
    {
        $user = $request->user();

        $applications = \App\Models\Application::where('role', 'parent')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('email', $user->email);
            })
            ->latest()
            ->get()
            ->map(fn ($app) => [
                'id'         => $app->id,
                'full_name'  => $app->full_name,
                'email'      => $app->email,
                'phone'      => $app->phone,
                'address'    => $app->address,
                'status'     => $app->status,
                'notes'      => $app->clean_notes,
                'metadata'   => $app->metadata,
                'created_at' => $app->created_at->toIso8601String(),
            ]);

        // Also get reports/requests
        $reports = \App\Models\Report::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'type'       => $r->type,
                'title'      => $r->title,
                'body'       => json_decode($r->body, true),
                'status'     => $r->status,
                'created_at' => $r->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'applications' => $applications,
                'requests'     => $reports,
            ],
        ]);
    }

    /**
     * Get parent profile settings
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $parent = $user->parentProfile;

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $parent?->phone,
                'address' => $parent?->address,
                'state' => $parent?->state,
            ]
        ]);
    }

    /**
     * Update parent profile information
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $parent = $user->parentProfile;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'state' => ['nullable', 'string', 'max:100'],
        ]);

        // Update user name and email
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update parent profile
        if ($parent) {
            $parent->update([
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? $parent->address,
                'state' => $validated['state'] ?? $parent->state,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $parent?->phone,
                'address' => $parent?->address,
                'state' => $parent?->state,
            ]
        ]);
    }

    /**
     * Update parent password
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!\Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => \Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }
}
