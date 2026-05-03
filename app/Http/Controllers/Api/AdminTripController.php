<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Services\TripService;

class AdminTripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::with(['driver.user', 'bus', 'route']);
        if ($status = $request->get('status')) { $query->where('status', $status); }
        if ($date = $request->get('date')) { $query->whereDate('trip_date', $date); }
        $trips = $query->latest('id')->get();
        $mapped = $trips->map(fn (Trip $t) => [
            'id' => $t->id, 'trip_date' => $t->trip_date?->toDateString(), 'shift' => $t->shift,
            'status' => $t->status, 'started_at' => $t->started_at, 'ended_at' => $t->ended_at,
            'driver' => $t->driver ? ['id' => $t->driver->id, 'name' => $t->driver->user?->name, 'user' => $t->driver->user] : null,
            'bus' => $t->bus ? ['id' => $t->bus->id, 'bus_number' => $t->bus->bus_number] : null,
            'route' => $t->route ? ['id' => $t->route->id, 'name' => $t->route->name] : null,
            'created_at' => $t->created_at,
        ]);
        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function show(Trip $trip)
    {
        $trip->load(['driver.user', 'bus', 'route', 'attendance.student']);
        return response()->json(['success' => true, 'data' => $trip]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'bus_route_id' => ['required', 'integer', 'exists:bus_routes,id'],
            'trip_date' => ['required', 'date'],
            'shift' => ['sometimes', 'string', 'in:morning,afternoon'],
            'status' => ['sometimes', 'string', 'in:assigned,active,completed,cancelled'],
        ]);
        $trip = Trip::create($data);
        $trip->load(['driver.user', 'bus', 'route']);
        return response()->json(['success' => true, 'data' => $trip, 'message' => 'Trip created'], 201);
    }

    public function update(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'driver_id' => ['sometimes', 'integer', 'exists:drivers,id'],
            'bus_id' => ['sometimes', 'integer', 'exists:buses,id'],
            'bus_route_id' => ['sometimes', 'integer', 'exists:bus_routes,id'],
            'trip_date' => ['sometimes', 'date'],
            'shift' => ['sometimes', 'string', 'in:morning,afternoon'],
            'status' => ['sometimes', 'string'],
        ]);
        $trip->update($data);
        return response()->json(['success' => true, 'data' => $trip->fresh(['driver.user', 'bus', 'route']), 'message' => 'Trip updated']);
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();
        return response()->json(['success' => true, 'message' => 'Trip deleted']);
    }

    public function start(Trip $trip, TripService $svc)
    {
        try { $trip = $svc->startTrip($trip); } catch (\DomainException $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 422); }
        return response()->json(['success' => true, 'data' => $trip, 'message' => 'Trip started']);
    }

    public function complete(Trip $trip, TripService $svc)
    {
        try { $trip = $svc->endTrip($trip); } catch (\DomainException $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 422); }
        return response()->json(['success' => true, 'data' => $trip, 'message' => 'Trip completed']);
    }

    public function cancel(Request $request, Trip $trip)
    {
        $trip->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'Trip cancelled']);
    }
}
