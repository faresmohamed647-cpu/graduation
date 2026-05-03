<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Driver> */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->driver(),
            'phone' => '01' . fake()->numerify('#########'),
            'license_number' => strtoupper(fake()->bothify('??-####-??')),
            'years_experience' => fake()->numberBetween(2, 20),
            'active' => true,
        ];
    }
}
