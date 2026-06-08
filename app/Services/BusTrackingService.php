<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\BusLocation;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class BusTrackingService
{
    public const ALEX_LAT_MIN = 31.05;
    public const ALEX_LAT_MAX = 31.35;
    public const ALEX_LNG_MIN = 29.75;
    public const ALEX_LNG_MAX = 30.10;

    public function isWithinAlexandria(float $lat, float $lng): bool
    {
        return $lat >= self::ALEX_LAT_MIN
            && $lat <= self::ALEX_LAT_MAX
            && $lng >= self::ALEX_LNG_MIN
            && $lng <= self::ALEX_LNG_MAX;
    }

    public function updateLocation(
        Bus $bus,
        ?Trip $trip,
        float $latitude,
        float $longitude,
        ?float $speed = null,
        ?float $heading = null,
    ): BusLocation {
        if (! $this->isWithinAlexandria($latitude, $longitude)) {
            throw new \DomainException('Location must be within Alexandria service area.');
        }

        $recordedAt = CarbonImmutable::now();

        $location = BusLocation::create([
            'bus_id' => $bus->id,
            'trip_id' => $trip?->id,
            'driver_id' => $trip?->driver_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'speed' => $speed,
            'heading' => $heading,
            'recorded_at' => $recordedAt,
        ]);

        $bus->update([
            'current_lat' => $latitude,
            'current_lng' => $longitude,
            'current_speed' => $speed,
            'current_heading' => $heading,
            'location_updated_at' => $recordedAt,
        ]);

        if ($trip) {
            $meta = $trip->meta ?? [];
            $meta['last_location'] = [
                'lat' => $latitude,
                'lng' => $longitude,
                'speed' => $speed,
                'at' => $recordedAt->toIso8601String(),
            ];
            $trip->update(['meta' => $meta]);
        }

        return $location;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function liveFleetSnapshot(): array
    {
        $today = CarbonImmutable::today();

        $activeTrips = Trip::with(['bus', 'route', 'driver.user'])
            ->whereDate('trip_date', $today)
            ->whereIn('status', ['active', 'assigned', 'in_progress'])
            ->orderByDesc('id')
            ->get()
            ->unique('bus_id')
            ->keyBy('bus_id');

        return Bus::query()
            ->where('active', true)
            ->orderBy('bus_number')
            ->get()
            ->map(function (Bus $bus) use ($activeTrips) {
                $trip = $activeTrips->get($bus->id);
                $stops = $trip?->route?->stops ?? [];

                return [
                    'bus_id' => $bus->id,
                    'bus_number' => $bus->bus_number,
                    'plate_number' => $bus->plate_number,
                    'route' => $trip?->route?->name ?? 'No active route',
                    'driver' => $trip?->driver?->user?->name ?? 'Unassigned',
                    'latitude' => $bus->current_lat !== null ? (float) $bus->current_lat : null,
                    'longitude' => $bus->current_lng !== null ? (float) $bus->current_lng : null,
                    'speed' => $bus->current_speed !== null ? round((float) $bus->current_speed, 1) : null,
                    'heading' => $bus->current_heading !== null ? (float) $bus->current_heading : null,
                    'status' => $this->resolveStatus($bus),
                    'last_update' => $bus->location_updated_at?->toIso8601String(),
                    'trip_id' => $trip?->id,
                    'trip_status' => $trip?->status,
                    'route_stops' => collect($stops)->sortBy('order')->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function resolveStatus(Bus $bus): string
    {
        if ($bus->current_lat === null || $bus->location_updated_at === null) {
            return 'inactive';
        }

        if ($bus->location_updated_at->lt(now()->subMinutes(5))) {
            return 'offline';
        }

        $speed = (float) ($bus->current_speed ?? 0);

        return $speed >= 3 ? 'moving' : 'stopped';
    }

    /**
     * @return Collection<int, BusLocation>
     */
    public function tripHistory(Trip $trip, int $limit = 500): Collection
    {
        return BusLocation::query()
            ->where('trip_id', $trip->id)
            ->orderByDesc('recorded_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }
}
