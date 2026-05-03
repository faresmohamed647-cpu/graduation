<?php

namespace Database\Factories;

use App\Models\BusRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BusRoute> */
class BusRouteFactory extends Factory
{
    protected $model = BusRoute::class;

    private static array $routeNames = [
        'Nasr City → Heliopolis', 'Maadi → Downtown Cairo', 'October City → Giza',
        'New Cairo → Zamalek', 'Mohandessin → Dokki', 'Rehab City → Nasr City',
        'Tagamoa → Ain Shams', 'Shorouk City → Abbassia',
    ];

    private static int $routeIndex = 0;

    public function definition(): array
    {
        $name = self::$routeNames[self::$routeIndex % count(self::$routeNames)];
        self::$routeIndex++;

        return [
            'name' => $name,
            'type' => fake()->randomElement(['morning', 'afternoon']),
            'stops' => [
                ['name' => 'Stop A - ' . fake()->streetName(), 'lat' => 30.04 + fake()->randomFloat(3, 0, 0.1), 'lng' => 31.23 + fake()->randomFloat(3, 0, 0.1), 'order' => 1],
                ['name' => 'Stop B - ' . fake()->streetName(), 'lat' => 30.05 + fake()->randomFloat(3, 0, 0.1), 'lng' => 31.24 + fake()->randomFloat(3, 0, 0.1), 'order' => 2],
                ['name' => 'Stop C - ' . fake()->streetName(), 'lat' => 30.06 + fake()->randomFloat(3, 0, 0.1), 'lng' => 31.25 + fake()->randomFloat(3, 0, 0.1), 'order' => 3],
            ],
            'estimated_minutes' => fake()->numberBetween(25, 60),
            'active' => true,
        ];
    }
}
