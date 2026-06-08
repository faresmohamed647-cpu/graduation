<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Trip;
use App\Services\BusTrackingService;
use Illuminate\Http\Request;

class AdminTrackingController extends Controller
{
    public function live(BusTrackingService $tracking)
    {
        return response()->json([
            'success' => true,
            'data' => $tracking->liveFleetSnapshot(),
            'center' => [
                'lat' => 31.2001,
                'lng' => 29.9187,
                'city' => 'Alexandria',
            ],
        ]);
    }

    public function bus(Bus $bus, BusTrackingService $tracking)
    {
        $todayTrip = Trip::with(['route', 'driver.user'])
            ->where('bus_id', $bus->id)
            ->whereDate('trip_date', now()->toDateString())
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'bus_id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'latitude' => $bus->current_lat !== null ? (float) $bus->current_lat : null,
                'longitude' => $bus->current_lng !== null ? (float) $bus->current_lng : null,
                'speed' => $bus->current_speed !== null ? (float) $bus->current_speed : null,
                'status' => $tracking->resolveStatus($bus),
                'last_update' => $bus->location_updated_at?->toIso8601String(),
                'trip' => $todayTrip,
                'route_stops' => $todayTrip?->route?->stops ?? [],
            ],
        ]);
    }

    public function tripHistory(Trip $trip, BusTrackingService $tracking, Request $request)
    {
        $points = $tracking->tripHistory($trip, (int) $request->get('limit', 500));

        return response()->json([
            'success' => true,
            'data' => $points->map(fn ($point) => [
                'lat' => (float) $point->latitude,
                'lng' => (float) $point->longitude,
                'speed' => $point->speed,
                'recorded_at' => $point->recorded_at?->toIso8601String(),
            ])->values(),
        ]);
    }
}
