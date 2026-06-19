<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\ServiceRequest;
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
        $assignedBus = null;
        $isApproved = false;
        $appStatus = 'pending';

        $driverProfile = $user?->driverProfile;
        if ($driverProfile) {
            $applications = $this->applicationsForUser($user, 'driver')->limit(10)->get();
            $acceptedApplication = $applications->firstWhere('status', 'accepted');
            $pendingApplication = $applications->firstWhere('status', 'pending');
            $rejectedApplication = $applications->firstWhere('status', 'rejected');

            if ($driverProfile->status === 'approved') {
                $isApproved = true;
                $appStatus = 'approved';
            } elseif ($driverProfile->status === 'pending_details') {
                $isApproved = false;
                $appStatus = 'pending_details';
            } elseif ($driverProfile->status === 'pending_approval') {
                $isApproved = false;
                $appStatus = 'pending_approval';
            } elseif ($driverProfile->status === 'rejected' || $rejectedApplication) {
                $isApproved = false;
                $appStatus = 'rejected';
            } elseif ($acceptedApplication || $driverProfile->active) {
                if (empty($driverProfile->status)) {
                    $driverProfile->update(['status' => 'pending_details']);
                }
                $isApproved = false;
                $appStatus = $driverProfile->status ?: 'pending_details';
            } else {
                $isApproved = false;
                $appStatus = 'pending';
            }

            if ($isApproved && $driverId) {
                $assignedBus = \App\Models\Bus::where('driver_id', $driverId)->first();
            }

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
        } else {
            $applications = collect([]);
        }

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
            'isApproved' => $isApproved,
            'appStatus' => $appStatus,
            'assignedBus' => $assignedBus,
            'dashboardSections' => $driverProfile?->resolvedDashboardSections() ?? \App\Models\Driver::DEFAULT_DASHBOARD_SECTIONS,
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
        $user = $request->user()->load('driverProfile');
        $driverProfile = $user->driverProfile;

        $assignedBus = null;
        $assignedRoute = null;
        $routes = collect();

        if ($driverProfile) {
            $assignedBus = Bus::query()
                ->where('driver_id', $driverProfile->id)
                ->with('route')
                ->first();

            $assignedRoute = $assignedBus?->route;

            $latestTrip = Trip::query()
                ->with('route')
                ->where('driver_id', $driverProfile->id)
                ->latest('trip_date')
                ->first();

            if (! $assignedRoute && $latestTrip?->route) {
                $assignedRoute = $latestTrip->route;
            }

            $routeIds = Trip::query()
                ->where('driver_id', $driverProfile->id)
                ->whereNotNull('bus_route_id')
                ->pluck('bus_route_id')
                ->unique()
                ->filter()
                ->values();

            $routes = BusRoute::query()
                ->where(function ($query) use ($driverProfile, $routeIds) {
                    $hasScope = false;
                    if ($driverProfile->school_id) {
                        $query->where('school_id', $driverProfile->school_id);
                        $hasScope = true;
                    }
                    if ($routeIds->isNotEmpty()) {
                        if ($hasScope) {
                            $query->orWhereIn('id', $routeIds);
                        } else {
                            $query->whereIn('id', $routeIds);
                            $hasScope = true;
                        }
                    }
                    if (! $hasScope) {
                        $query->whereRaw('1 = 0');
                    }
                })
                ->orderBy('name')
                ->get()
                ->unique('id')
                ->values();

            if ($assignedRoute && ! $routes->contains('id', $assignedRoute->id)) {
                $routes->prepend($assignedRoute);
            }
        }

        $serviceRequests = ServiceRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $employmentStatus = match ($driverProfile?->status ?? 'pending') {
            'approved' => 'active',
            'rejected' => 'inactive',
            'pending_approval' => 'pending',
            default => 'pending',
        };

        $requestContext = [
            'driverName' => $driverProfile?->full_name ?? $user->name ?? '',
            'driverEmail' => $user->email ?? '',
            'driverPhone' => $driverProfile?->phone ?? '',
            'driverLicense' => $driverProfile?->license_number ?? '',
            'employeeId' => $driverProfile
                ? 'DRV-' . str_pad((string) $driverProfile->id, 3, '0', STR_PAD_LEFT)
                : '',
            'busNumber' => $assignedBus?->bus_number ? 'Bus #' . $assignedBus->bus_number : '',
            'route' => $assignedRoute?->name ?? '',
            'yearsExperience' => $driverProfile?->years_experience ?? '',
            'employmentStatus' => $employmentStatus,
        ];

        return view('driver.driver-request', [
            'user' => $user,
            'driverProfile' => $driverProfile,
            'assignedBus' => $assignedBus,
            'assignedRoute' => $assignedRoute,
            'routes' => $routes,
            'serviceRequests' => $serviceRequests,
            'requestContext' => $requestContext,
            'employmentStatus' => $employmentStatus,
            'apiToken' => $this->dashboardApiToken($user),
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