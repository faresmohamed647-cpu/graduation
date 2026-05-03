<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Trip> */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'bus_id' => Bus::factory(),
            'bus_route_id' => BusRoute::factory(),
            'trip_date' => now()->toDateString(),
            'shift' => fake()->randomElement(['morning', 'afternoon']),
            'status' => 'assigned',
            'started_at' => null,
            'ended_at' => null,
            'meta' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active', 'started_at' => now()->subMinutes(30)]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed', 'started_at' => now()->subHours(2), 'ended_at' => now()->subHour()]);
    }
}
