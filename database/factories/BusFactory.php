<?php

namespace Database\Factories;

use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Bus> */
class BusFactory extends Factory
{
    protected $model = Bus::class;

    public function definition(): array
    {
        return [
            'bus_number' => 'BUS-' . fake()->unique()->numerify('###'),
            'plate_number' => strtoupper(fake()->unique()->bothify('??? ####')),
            'capacity' => fake()->randomElement([30, 40, 45, 50, 55]),
            'active' => true,
        ];
    }
}
