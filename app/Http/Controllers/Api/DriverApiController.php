<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Attendance;
use App\Services\TripService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DriverApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (!$driverId) { return response()->json(['success' => true, 'data' => ['today_trip' => null, 'message' => 'No driver profile']]); }

        $trip = Trip::with(['bus', 'route', 'attendance.student'])
            ->where('driver_id', $driverId)->whereDate('trip_date', CarbonImmutable::today())
            ->latest('id')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'today_trip' => $trip,
                'trip_status' => $trip?->status,
                'students' => $trip?->attendance?->map(fn ($a) => [
                    'student_id' => $a->student_id, 'name' => $a->student?->full_name,
                    'status' => $a->status,
                    'picked_up_at' => $a->picked_up_at?->toIso8601String(),
                    'dropped_off_at' => $a->dropped_off_at?->toIso8601String(),
                ])->values() ?? [],
            ],
        ]);
    }

    public function todayTrips(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (!$driverId) { return response()->json(['success' => true, 'data' => []]); }
        $trips = Trip::with(['bus', 'route'])->where('driver_id', $driverId)
            ->whereDate('trip_date', CarbonImmutable::today())->get();
        return response()->json(['success' => true, 'data' => $trips]);
    }

    public function startTrip(Request $request, Trip $trip, TripService $svc)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (!$driverId || $trip->driver_id !== $driverId) { abort(403); }
        try { $trip = $svc->startTrip($trip); } catch (\DomainException $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 422); }
        return response()->json(['success' => true, 'data' => $trip]);
    }

    public function completeTrip(Request $request, Trip $trip, TripService $svc)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (!$driverId || $trip->driver_id !== $driverId) { abort(403); }
        try { $trip = $svc->endTrip($trip); } catch (\DomainException $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 422); }
        return response()->json(['success' => true, 'data' => $trip]);
    }

    public function tripStudents(Request $request, Trip $trip)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (!$driverId || $trip->driver_id !== $driverId) { abort(403); }
        $attendance = $trip->attendance()->with('student')->get();
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function markAttendance(Request $request, Trip $trip, TripService $svc)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (!$driverId || $trip->driver_id !== $driverId) { abort(403); }
        $data = $request->validate(['student_id' => ['required', 'integer'], 'action' => ['required', 'in:pickup,dropoff']]);
        try {
            $att = $data['action'] === 'pickup'
                ? $svc->markPickedUp($trip, $data['student_id'])
                : $svc->markDroppedOff($trip, $data['student_id']);
        } catch (\DomainException $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 422); }
        return response()->json(['success' => true, 'data' => $att]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate(['latitude' => ['required', 'numeric'], 'longitude' => ['required', 'numeric']]);
        return response()->json(['success' => true, 'message' => 'Location updated']);
    }

    public function myStudents(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (!$driverId) { return response()->json(['success' => true, 'data' => []]); }
        $trip = Trip::with('attendance.student')->where('driver_id', $driverId)
            ->whereDate('trip_date', CarbonImmutable::today())->latest('id')->first();
        $students = $trip?->attendance?->map(fn ($a) => $a->student)->filter()->values() ?? collect();
        return response()->json(['success' => true, 'data' => $students]);
    }

    public function notifications(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()->notifications()->latest()->limit(30)->get()]);
    }
}
