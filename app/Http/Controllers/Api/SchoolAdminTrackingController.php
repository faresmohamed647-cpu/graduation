<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Trip;
use App\Services\BusTrackingService;
use Illuminate\Http\Request;

class SchoolAdminTrackingController extends Controller
{
    use ResolvesSchoolScope;

    public function live(Request $request, BusTrackingService $tracking)
    {
        $schoolId = $this->schoolId($request);
        $schoolBusIds = Bus::where('school_id', $schoolId)->pluck('id')->all();
        $fleet = collect($tracking->liveFleetSnapshot())
            ->filter(fn (array $item) => in_array($item['bus_id'] ?? null, $schoolBusIds, true))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $fleet,
            'center' => ['lat' => 31.2001, 'lng' => 29.9187, 'city' => 'Alexandria'],
        ]);
    }

    public function bus(Request $request, Bus $bus, BusTrackingService $tracking)
    {
        abort_unless((int) $bus->school_id === $this->schoolId($request), 403);

        $todayTrip = Trip::with(['route', 'driver.user', 'attendance.student'])
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
                'attendance' => $todayTrip?->attendance?->map(fn ($a) => [
                    'student' => $a->student?->full_name,
                    'status' => $a->status,
                ])->values(),
            ],
        ]);
    }

    public function tripHistory(Request $request, Trip $trip, BusTrackingService $tracking)
    {
        abort_unless((int) $trip->school_id === $this->schoolId($request), 403);

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
