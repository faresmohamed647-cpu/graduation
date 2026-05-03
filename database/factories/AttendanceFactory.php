<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Attendance> */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'student_id' => Student::factory(),
            'status' => 'absent',
            'picked_up_at' => null,
            'dropped_off_at' => null,
        ];
    }

    public function pickedUp(): static
    {
        return $this->state(fn () => ['status' => 'picked_up', 'picked_up_at' => now()->subMinutes(fake()->numberBetween(10, 60))]);
    }

    public function droppedOff(): static
    {
        return $this->state(fn () => [
            'status' => 'dropped_off',
            'picked_up_at' => now()->subHours(2),
            'dropped_off_at' => now()->subHour(),
        ]);
    }
}
