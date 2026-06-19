<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Attendance;
use App\Services\BusTrackingService;
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

    public function updateLocation(Request $request, BusTrackingService $tracking)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (! $driverId) {
            abort(403);
        }

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'speed' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'min:0', 'max:360'],
        ]);

        $trip = Trip::with('bus')
            ->where('driver_id', $driverId)
            ->whereDate('trip_date', CarbonImmutable::today())
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (! $trip || ! $trip->bus) {
            return response()->json([
                'success' => false,
                'message' => 'No active trip found. Start your trip before sending GPS.',
            ], 422);
        }

        try {
            $location = $tracking->updateLocation(
                $trip->bus,
                $trip,
                (float) $data['latitude'],
                (float) $data['longitude'],
                isset($data['speed']) ? (float) $data['speed'] : null,
                isset($data['heading']) ? (float) $data['heading'] : null,
            );
        } catch (\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Location updated',
            'data' => [
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'speed' => $location->speed,
                'recorded_at' => $location->recorded_at?->toIso8601String(),
            ],
        ]);
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

    /**
     * Get driver's applications/requests from the database.
     */
    public function routeProgress(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (! $driverId) {
            return response()->json(['success' => true, 'data' => null]);
        }

        $trip = Trip::with(['route', 'bus', 'attendance'])
            ->where('driver_id', $driverId)
            ->whereDate('trip_date', CarbonImmutable::today())
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (! $trip) {
            return response()->json(['success' => true, 'data' => null]);
        }

        $stops = $trip->route?->stops ?? [];
        $totalStops = count($stops);
        $completed = $trip->attendance->whereIn('status', ['picked_up', 'dropped_off'])->count();
        $totalStudents = max($trip->attendance->count(), 1);

        return response()->json([
            'success' => true,
            'data' => [
                'trip_id' => $trip->id,
                'route_name' => $trip->route?->name,
                'stops_total' => $totalStops,
                'students_total' => $trip->attendance->count(),
                'students_completed' => $completed,
                'progress_percent' => round(($completed / $totalStudents) * 100, 1),
            ],
        ]);
    }

    public function performance(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (! $driverId) {
            return response()->json(['success' => true, 'data' => ['score' => 0]]);
        }

        $completed = Trip::where('driver_id', $driverId)->where('status', 'completed')->count();
        $total = Trip::where('driver_id', $driverId)->count();
        $onTime = Trip::where('driver_id', $driverId)->where('status', 'completed')->whereNotNull('ended_at')->count();
        $score = $total > 0 ? round((($onTime / max($total, 1)) * 0.6 + ($completed / max($total, 1)) * 0.4) * 100, 1) : 85;

        return response()->json([
            'success' => true,
            'data' => [
                'score' => $score,
                'completed_trips' => $completed,
                'total_trips' => $total,
                'fuel_tracking_ready' => true,
            ],
        ]);
    }

    public function maintenanceAlerts(Request $request)
    {
        $driverId = $request->user()?->driverProfile?->id;
        if (! $driverId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $busIds = \App\Models\Bus::where('driver_id', $driverId)->pluck('id');
        $records = \App\Models\MaintenanceRecord::with('bus:id,bus_number')
            ->whereIn('bus_id', $busIds)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();

        $insuranceAlerts = \App\Models\Bus::whereIn('id', $busIds)
            ->whereNotNull('insurance_expiry')
            ->where('insurance_expiry', '<=', now()->addDays(30))
            ->get()
            ->map(fn ($b) => [
                'type' => 'insurance',
                'bus_number' => $b->bus_number,
                'expiry' => $b->insurance_expiry?->toDateString(),
                'message' => 'Insurance expires soon for ' . $b->bus_number,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'maintenance' => $records,
                'insurance' => $insuranceAlerts,
            ],
        ]);
    }

    public function requests(Request $request)
    {
        $user = $request->user();

        $applications = \App\Models\Application::where('role', 'driver')
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

    public function submitDetails(Request $request)
    {
        $user = $request->user();
        $driver = $user?->driverProfile;

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found.',
            ], 404);
        }

        if (! in_array($driver->status, ['pending_details', 'rejected'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Profile details were already submitted or approved.',
            ], 422);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'age' => ['required', 'integer', 'min:20', 'max:70'],
            'gender' => ['required', 'string', 'in:male,female'],
            'license_number' => ['required', 'string', 'max:50'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:40'],
            'car_type' => ['required', 'string', 'max:100'],
            'car_model' => ['required', 'string', 'max:100'],
            'car_plate' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'national_id' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'criminal_record' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        $nationalIdPath = null;
        if ($request->hasFile('national_id')) {
            $nationalIdPath = $request->file('national_id')->store('drivers/documents', 'public');
        }

        $criminalRecordPath = null;
        if ($request->hasFile('criminal_record')) {
            $criminalRecordPath = $request->file('criminal_record')->store('drivers/documents', 'public');
        }

        $updateData = array_diff_key($validated, array_flip(['national_id', 'criminal_record']));
        $updateData['national_id_path'] = $nationalIdPath;
        $updateData['criminal_record_path'] = $criminalRecordPath;
        $updateData['status'] = 'pending_approval';
        $updateData['active'] = false;

        $driver->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Driver profile details submitted successfully.',
            'data' => $driver,
        ]);
    }

    public function profileStatus(Request $request)
    {
        $driver = $request->user()?->driverProfile;
        $status = $driver?->status ?? 'pending';

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $status,
                'is_dashboard_unlocked' => $status === 'approved',
            ],
        ]);
    }
}
