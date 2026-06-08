<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<School> */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        $name = fake()->company() . ' School';

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'phone' => '01' . fake()->numerify('#########'),
            'address' => fake()->address(),
            'principal_name' => fake()->name(),
            'status' => 'active',
        ];
    }
}
