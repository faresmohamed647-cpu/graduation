<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\ParentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    private static array $egyptianFirstNames = [
        'Youssef', 'Omar', 'Adam', 'Ali', 'Ziad', 'Hamza', 'Kareem',
        'Nour', 'Malak', 'Farida', 'Hana', 'Salma', 'Jana', 'Layla',
        'Maryam', 'Aya', 'Yasmin', 'Taha', 'Mostafa', 'Khaled',
    ];

    private static array $schools = [
        'Al-Azhar International School',
        'Cairo American College',
        'British International School Cairo',
        'Maadi STEM School',
        'Nile Egyptian Schools',
        'El Alsson International School',
    ];

    public function definition(): array
    {
        $firstName = fake()->randomElement(self::$egyptianFirstNames);
        $lastName = fake()->randomElement(['Ahmed', 'Mohamed', 'Hassan', 'Ali', 'Ibrahim', 'Khaled', 'Mostafa', 'Samir']);

        return [
            'parent_id' => ParentProfile::factory(),
            'full_name' => "{$firstName} {$lastName}",
            'grade' => 'Grade ' . fake()->numberBetween(1, 12),
            'school_name' => fake()->randomElement(self::$schools),
            'active' => true,
        ];
    }
}
