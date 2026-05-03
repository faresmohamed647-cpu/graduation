<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function dashboard(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;

        $todayTrip = null;
        if ($driverId) {
            $todayTrip = Trip::query()
                ->with(['bus', 'route'])
                ->where('driver_id', $driverId)
                ->whereDate('trip_date', CarbonImmutable::today())
                ->latest('id')
                ->first();
        }

        return view('driver.driver', compact('todayTrip'));
    }

    public function report()
    {
        return view('driver.report');
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
}

