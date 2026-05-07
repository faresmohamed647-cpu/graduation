<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $driverId = $user?->driverProfile?->id;

        $today = CarbonImmutable::today();
        $todayTrip = null;
        $trips = collect([]);

        if ($driverId) {
            $todayTrip = Trip::query()
                ->with(['bus', 'route', 'attendance.student'])
                ->where('driver_id', $driverId)
                ->whereDate('trip_date', $today)
                ->latest('id')
                ->first();

            $trips = Trip::query()
                ->with(['bus', 'route'])
                ->where('driver_id', $driverId)
                ->latest('trip_date')
                ->limit(20)
                ->get();
        }

        $applications = $this->applicationsForUser($user, 'driver')->limit(10)->get();

        $stats = [
            'trips_today' => $trips->where('trip_date', $today->format('Y-m-d'))->count(),
            'trips_total' => $trips->count(),
            'trips_completed' => $trips->where('status', 'completed')->count(),
            'applications_pending' => $applications->where('status', 'pending')->count(),
        ];

        $apiToken = session('api_token');
        if (!$apiToken && auth()->check()) {
            $user->tokens()->where('name', 'dashboard-session')->delete();
            $apiToken = $user->createToken('dashboard-session')->plainTextToken;
            session(['api_token' => $apiToken]);
        }

        return view('driver.driver', [
            'userName' => $user?->name ?? 'Driver',
            'apiToken' => $apiToken ?? '',
            'todayTrip' => $todayTrip,
            'trips' => $trips,
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }

    public function report(Request $request)
    {
        $user = $request->user();
        $driverId = $user?->driverProfile?->id;

        $trips = collect([]);
        if ($driverId) {
            $trips = Trip::with(['bus', 'route'])
                ->where('driver_id', $driverId)
                ->latest('trip_date')
                ->limit(50)
                ->get();
        }

        return view('driver.report', compact('trips'));
    }

    public function requests(Request $request)
    {
        $user = $request->user();
        $applications = $this->applicationsForUser($user, 'driver')->get();

        return view('driver.driver-request', [
            'applications' => $applications,
            'user' => $user,
            'apiToken' => session('api_token', ''),
        ]);
    }

    public function applications(Request $request)
    {
        $user = $request->user();
        $applications = $this->applicationsForUser($user, 'driver')->paginate(10);
        $apiToken = $this->dashboardApiToken($user);

        return view('dashboard.driver-applications', compact('applications', 'apiToken'));
    }

    public function dashboardData(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;

        if (! $driverId) {
            return response()->json([
                'today_trip' => null,
                'message' => 'Driver profile missing.',
            ], 200);
        }

        $trip = Trip::query()
            ->with(['bus', 'route', 'attendance.student'])
            ->where('driver_id', $driverId)
            ->whereDate('trip_date', CarbonImmutable::today())
            ->latest('id')
            ->first();

        return response()->json([
            'today_trip' => $trip,
            'trip_status' => $trip?->status,
            'students' => $trip?->attendance?->map(fn ($a) => [
                'student_id' => $a->student_id,
                'name' => $a->student?->full_name,
                'status' => $a->status,
                'picked_up_at' => optional($a->picked_up_at)->toIso8601String(),
                'dropped_off_at' => optional($a->dropped_off_at)->toIso8601String(),
            ])->values() ?? [],
        ]);
    }

    public function tripStatus(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;

        if (! $driverId) {
            return response()->json(['trip' => null], 200);
        }

        $trip = Trip::query()
            ->with(['bus', 'route'])
            ->where('driver_id', $driverId)
            ->whereDate('trip_date', CarbonImmutable::today())
            ->latest('id')
            ->first();

        return response()->json([
            'trip' => $trip,
            'status' => $trip?->status,
            'started_at' => optional($trip?->started_at)->toIso8601String(),
            'ended_at' => optional($trip?->ended_at)->toIso8601String(),
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
