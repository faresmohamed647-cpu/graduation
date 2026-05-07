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
            'latestTrip' => $latestTrip,
            'stats' => $stats,
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
