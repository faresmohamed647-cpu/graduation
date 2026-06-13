<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Attendance;
use App\Models\Trip;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $parentId = $user?->parentProfile?->id;

        $children = collect([]);
        $attendanceRecords = collect([]);
        $latestTrip = null;
        $assignedChildrenCount = 0;
        $assignedBus = null;
        $assignedDriver = null;
        $assignedRoute = null;

        if ($parentId) {
            $children = Student::where('parent_id', $parentId)
                ->where('active', true)
                ->with(['bus.driver.user', 'route'])
                ->orderBy('full_name')
                ->get();

            $assignedChildrenCount = Student::where('parent_id', $parentId)
                ->whereNotNull('bus_id')
                ->count();

            $firstAssignedStudent = Student::where('parent_id', $parentId)
                ->whereNotNull('bus_id')
                ->with(['bus.driver.user', 'route'])
                ->first();

            if ($firstAssignedStudent) {
                $assignedBus = $firstAssignedStudent->bus;
                $assignedDriver = $assignedBus?->driver;
                $assignedRoute = $firstAssignedStudent->route;
            }

            $childIds = $children->pluck('id')->all();
            if ($childIds) {
                $attendanceRecords = Attendance::with(['student', 'trip'])
                    ->whereIn('student_id', $childIds)
                    ->latest()
                    ->limit(20)
                    ->get();
            }

            $latestTrip = Trip::query()
                ->with(['driver.user', 'bus', 'route'])
                ->whereDate('trip_date', '<=', CarbonImmutable::today())
                ->whereHas('attendance', function ($q) use ($childIds) {
                    $q->whereIn('student_id', $childIds);
                })
                ->latest('id')
                ->first();
        }

        $applications = $this->applicationsForUser($user, 'parent')->limit(10)->get();
        $acceptedApplication = $applications->firstWhere('status', 'accepted');
        $pendingApplication = $applications->firstWhere('status', 'pending');
        $rejectedApplication = $applications->firstWhere('status', 'rejected');

        $isApproved = false;
        $appStatus = 'pending';

        if ($user?->parentProfile) {
            if ($user->parentProfile->active || $acceptedApplication) {
                $isApproved = true;
                $appStatus = 'approved';
            } elseif ($rejectedApplication) {
                $appStatus = 'rejected';
            } else {
                $appStatus = 'pending';
            }
        }

        $childFormCount = (int) (
            $user?->parentProfile?->student_count
            ?: ($acceptedApplication?->metadata['student_count'] ?? 1)
        );
        $childFormCount = max(1, min($childFormCount, 10));

        $stats = [
            'children_count' => $children->count(),
            'attendance_present' => $attendanceRecords->where('status', 'present')->count(),
            'attendance_absent' => $attendanceRecords->where('status', 'absent')->count(),
            'applications_pending' => $applications->where('status', 'pending')->count(),
        ];

        $apiToken = session('api_token');
        if (!$apiToken && auth()->check()) {
            $user->tokens()->where('name', 'dashboard-session')->delete();
            $apiToken = $user->createToken('dashboard-session')->plainTextToken;
            session(['api_token' => $apiToken]);
        }

        return view('parent.parent', [
            'userName' => $user?->name ?? 'Parent',
            'apiToken' => $apiToken ?? '',
            'children' => $children,
            'attendanceRecords' => $attendanceRecords,
            'applications' => $applications,
            'acceptedApplication' => $acceptedApplication,
            'childFormCount' => $childFormCount,
            'latestTrip' => $latestTrip,
            'stats' => $stats,
            'isApproved' => $isApproved,
            'appStatus' => $appStatus,
            'assignedChildrenCount' => $assignedChildrenCount,
            'assignedBus' => $assignedBus,
            'assignedDriver' => $assignedDriver,
            'assignedRoute' => $assignedRoute,
        ]);
    }

    public function report(Request $request)
    {
        $user = $request->user();
        $parentId = $user?->parentProfile?->id;

        $children = collect([]);
        $attendanceRecords = collect([]);

        if ($parentId) {
            $children = Student::where('parent_id', $parentId)
                ->where('active', true)
                ->orderBy('full_name')
                ->get();

            $childIds = $children->pluck('id')->all();
            if ($childIds) {
                $attendanceRecords = Attendance::with(['student', 'trip'])
                    ->whereIn('student_id', $childIds)
                    ->latest()
                    ->limit(50)
                    ->get();
            }
        }

        return view('parent.report', compact('children', 'attendanceRecords'));
    }

    public function requests(Request $request)
    {
        $user = $request->user();
        $applications = $this->applicationsForUser($user, 'parent')->get();

        return view('parent.parent-request', [
            'applications' => $applications,
            'user' => $user,
            'apiToken' => session('api_token', ''),
        ]);
    }

    public function applications(Request $request)
    {
        $user = $request->user();
        $applications = $this->applicationsForUser($user, 'parent')->paginate(10);
        $apiToken = $this->dashboardApiToken($user);

        return view('dashboard.parent-applications', compact('applications', 'apiToken'));
    }

    public function dashboardData(Request $request)
    {
        $user = $request->user();
        $parentId = $user?->parentProfile?->id;

        if (! $parentId) {
            return response()->json([
                'children' => [],
                'latest_trip' => null,
                'notifications' => [],
                'message' => 'Parent profile missing.',
            ], 200);
        }

        $children = Student::query()
            ->where('parent_id', $parentId)
            ->where('active', true)
            ->orderBy('full_name')
            ->get()
            ->map(fn (Student $s) => [
                'id' => $s->id,
                'full_name' => $s->full_name,
                'grade' => $s->grade,
                'school_name' => $s->school_name,
            ])
            ->values();

        // Latest trip for any of the parent's children (based on attendance join)
        $latestTrip = Trip::query()
            ->whereDate('trip_date', '<=', CarbonImmutable::today())
            ->whereHas('attendance', function ($q) use ($children) {
                $q->whereIn('student_id', $children->pluck('id')->all());
            })
            ->latest('id')
            ->first();

        return response()->json([
            'children' => $children,
            'latest_trip' => $latestTrip,
            'notifications' => $user->notifications()->latest()->limit(20)->get(),
        ]);
    }

    private function applicationsForUser($user, string $role)
    {
        return Application::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->where(DB::raw('LOWER(role)'), strtolower($role))
            ->latest();
    }

    private function dashboardApiToken($user): string
    {
        $apiToken = session('api_token');
        if (!$apiToken && $user) {
            $user->tokens()->where('name', 'dashboard-session')->delete();
            $apiToken = $user->createToken('dashboard-session')->plainTextToken;
            session(['api_token' => $apiToken]);
        }

        return $apiToken ?? '';
    }
}
