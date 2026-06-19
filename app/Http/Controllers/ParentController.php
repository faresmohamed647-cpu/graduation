<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Attendance;
use App\Models\ServiceRequest;
use App\Models\Student;
use App\Models\Trip;
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
        $isDashboardUnlocked = false;

        if ($user?->parentProfile) {
            $parentProfile = $user->parentProfile->fresh();
            $profileStatus = strtolower(trim((string) ($parentProfile->status ?? 'pending')));
            $hasChildren = $parentProfile->students()->exists();
            $profileApprovedAt = $parentProfile->profile_approved_at;

            if ($profileStatus === 'rejected' || $rejectedApplication) {
                $isApproved = false;
                $isDashboardUnlocked = false;
                $appStatus = 'rejected';
            } elseif ($profileApprovedAt || $profileStatus === 'approved') {
                if (! $profileApprovedAt) {
                    $parentProfile->update([
                        'profile_approved_at' => now(),
                        'status' => 'approved',
                        'active' => true,
                    ]);
                } elseif ($profileStatus !== 'approved' || ! $parentProfile->active) {
                    $parentProfile->update([
                        'status' => 'approved',
                        'active' => true,
                    ]);
                }
                $isApproved = true;
                $isDashboardUnlocked = true;
                $appStatus = 'approved';
            } elseif ($profileStatus === 'pending_approval') {
                $appStatus = 'pending_approval';
            } elseif ($profileStatus === 'pending_details') {
                $appStatus = 'pending_details';
            } elseif ($hasChildren && $acceptedApplication) {
                if ($profileStatus !== 'pending_approval') {
                    $parentProfile->update(['status' => 'pending_approval', 'active' => false]);
                }
                $appStatus = 'pending_approval';
            } elseif ($acceptedApplication) {
                if ($profileStatus !== 'pending_details') {
                    $parentProfile->update(['status' => 'pending_details', 'active' => false]);
                }
                $appStatus = 'pending_details';
            } else {
                $appStatus = 'pending';
            }
        }

        $childFormCount = (int) (
            $user?->parentProfile?->student_count
            ?: ($acceptedApplication?->metadata['student_count'] ?? 1)
        );
        $childFormCount = max(1, min($childFormCount, 10));

        $profileApprovedAt = $user?->parentProfile?->fresh()?->profile_approved_at;

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
            'isDashboardUnlocked' => $isDashboardUnlocked,
            'appStatus' => $appStatus,
            'assignedChildrenCount' => $assignedChildrenCount,
            'assignedBus' => $assignedBus,
            'assignedDriver' => $assignedDriver,
            'assignedRoute' => $assignedRoute,
            'profileApprovedAt' => $profileApprovedAt,
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
        $user = $request->user()->load('parentProfile');
        $parentProfile = $user->parentProfile;

        $children = collect();
        $schools = collect();

        if ($parentProfile) {
            $children = Student::query()
                ->where('parent_id', $parentProfile->id)
                ->where('active', true)
                ->with(['bus', 'route'])
                ->orderBy('full_name')
                ->get();

            $schools = $children->pluck('school_name')
                ->filter()
                ->merge([$parentProfile->school_name])
                ->filter()
                ->unique()
                ->values();
        }

        $serviceRequests = ServiceRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $requestContext = [
            'parentName' => $user->name ?? '',
            'parentEmail' => $user->email ?? '',
            'parentPhone' => $parentProfile?->phone ?? '',
            'parentLocation' => $parentProfile?->address ?? '',
            'schools' => $schools->values()->all(),
            'children' => $children->map(fn (Student $c) => [
                'id' => $c->id,
                'full_name' => $c->full_name,
                'grade' => $c->grade,
                'school_name' => $c->school_name,
                'bus' => $c->bus?->bus_number,
            ])->values()->all(),
        ];

        return view('parent.parent-request', [
            'user' => $user,
            'parentProfile' => $parentProfile,
            'children' => $children,
            'schools' => $schools,
            'serviceRequests' => $serviceRequests,
            'requestContext' => $requestContext,
            'apiToken' => $this->dashboardApiToken($user),
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
