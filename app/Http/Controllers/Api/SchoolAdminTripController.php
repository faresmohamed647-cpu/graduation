<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SchoolAdminTripController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $query = Trip::where('school_id', $schoolId)
            ->with(['driver.user', 'bus', 'route', 'attendance.student'])
            ->latest('trip_date');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('trip_date', $request->get('date'));
        }

        $trips = $query->paginate((int) $request->get('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => collect($trips->items())->map(fn (Trip $trip) => $this->mapTrip($trip)),
            'meta' => [
                'current_page' => $trips->currentPage(),
                'last_page' => $trips->lastPage(),
                'total' => $trips->total(),
            ],
        ]);
    }

    public function show(Request $request, Trip $trip)
    {
        $this->authorizeSchoolTrip($request, $trip);
        $trip->load(['driver.user', 'bus', 'route', 'attendance.student']);

        return response()->json(['success' => true, 'data' => $this->mapTrip($trip)]);
    }

    public function store(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'bus_route_id' => ['required', 'integer', 'exists:bus_routes,id'],
            'trip_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:morning,afternoon,evening'],
            'status' => ['sometimes', 'string', 'in:assigned,active,completed,cancelled'],
        ]);

        $data['school_id'] = $schoolId;
        $trip = Trip::create($data);
        $logger->log($request, 'trip.created', $trip);

        return response()->json(['success' => true, 'data' => $this->mapTrip($trip->load(['driver.user', 'bus', 'route'])), 'message' => 'Trip created'], 201);
    }

    public function update(Request $request, Trip $trip, ActivityLogService $logger)
    {
        $this->authorizeSchoolTrip($request, $trip);
        $data = $request->validate([
            'driver_id' => ['sometimes', 'integer', 'exists:drivers,id'],
            'bus_id' => ['sometimes', 'integer', 'exists:buses,id'],
            'bus_route_id' => ['sometimes', 'integer', 'exists:bus_routes,id'],
            'trip_date' => ['sometimes', 'date'],
            'shift' => ['sometimes', 'string', 'in:morning,afternoon,evening'],
            'status' => ['sometimes', 'string', 'in:assigned,active,completed,cancelled'],
        ]);

        $trip->update($data);
        $logger->log($request, 'trip.updated', $trip);

        return response()->json(['success' => true, 'data' => $this->mapTrip($trip->fresh(['driver.user', 'bus', 'route'])), 'message' => 'Trip updated']);
    }

    public function destroy(Request $request, Trip $trip, ActivityLogService $logger)
    {
        $this->authorizeSchoolTrip($request, $trip);
        $trip->delete();
        $logger->log($request, 'trip.deleted', null, ['trip_id' => $trip->id]);

        return response()->json(['success' => true, 'message' => 'Trip deleted']);
    }

    private function authorizeSchoolTrip(Request $request, Trip $trip): void
    {
        abort_unless((int) $trip->school_id === $this->schoolId($request), 403);
    }

    private function mapTrip(Trip $trip): array
    {
        return [
            'id' => $trip->id,
            'trip_date' => $trip->trip_date?->toDateString(),
            'shift' => $trip->shift,
            'status' => $trip->status,
            'started_at' => $trip->started_at?->toIso8601String(),
            'ended_at' => $trip->ended_at?->toIso8601String(),
            'driver' => $trip->driver?->user?->name,
            'driver_id' => $trip->driver_id,
            'bus' => $trip->bus?->bus_number,
            'bus_id' => $trip->bus_id,
            'route' => $trip->route?->name,
            'bus_route_id' => $trip->bus_route_id,
            'students_count' => $trip->attendance?->count() ?? 0,
            'attendance' => $trip->attendance?->map(fn ($a) => [
                'student' => $a->student?->full_name,
                'status' => $a->status,
                'picked_up_at' => $a->picked_up_at,
                'dropped_off_at' => $a->dropped_off_at,
            ])->values(),
        ];
    }
}
