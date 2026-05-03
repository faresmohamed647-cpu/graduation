<?php

namespace Database\Factories;

use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ParentProfile> */
class ParentProfileFactory extends Factory
{
    protected $model = ParentProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->parent(),
            'phone' => '01' . fake()->numerify('#########'),
            'address' => fake()->address(),
            'active' => true,
        ];
    }
}
