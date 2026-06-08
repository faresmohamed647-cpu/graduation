<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\EmergencyAlert;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LargeScaleSeeder extends Seeder
{
    public function run(): void
    {
        if (School::count() >= 50) {
            $this->command?->info('Large-scale data already present. Skipping.');

            return;
        }

        $this->command?->info('Seeding large-scale SafeStep data...');

        $schools = School::factory()->count(50)->create();

        foreach ($schools as $index => $school) {
            User::updateOrCreate(
                ['email' => 'school.admin.' . $school->id . '@safestep.com'],
                [
                    'name' => $school->principal_name ?? 'School Admin',
                    'password' => Hash::make('password'),
                    'plain_password' => 'password',
                    'role' => 'school_admin',
                    'school_id' => $school->id,
                ]
            );

            $drivers = collect();
            for ($d = 0; $d < 10; $d++) {
                $user = User::updateOrCreate(
                    ['email' => "driver.{$school->id}.{$d}@safestep.com"],
                    [
                        'name' => fake()->name(),
                        'password' => Hash::make('password'),
                        'plain_password' => 'password',
                        'role' => 'driver',
                    ]
                );
                $drivers->push(Driver::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'school_id' => $school->id,
                        'phone' => '01' . fake()->numerify('#########'),
                        'license_number' => 'DR-' . Str::upper(Str::random(6)),
                        'years_experience' => rand(2, 15),
                        'active' => true,
                        'status' => 'approved',
                    ]
                ));
            }

            $buses = collect();
            for ($b = 0; $b < 10; $b++) {
                $buses->push(Bus::updateOrCreate(
                    ['bus_number' => "S{$school->id}-BUS-" . str_pad((string) ($b + 1), 3, '0', STR_PAD_LEFT)],
                    [
                        'school_id' => $school->id,
                        'driver_id' => $drivers[$b % $drivers->count()]->id,
                        'plate_number' => strtoupper(Str::random(3)) . ' ' . rand(1000, 9999),
                        'capacity' => rand(30, 50),
                        'active' => true,
                        'status' => 'active',
                        'insurance_expiry' => now()->addDays(rand(30, 365)),
                    ]
                ));
            }

            $routes = collect();
            for ($r = 0; $r < 10; $r++) {
                $routes->push(BusRoute::updateOrCreate(
                    ['school_id' => $school->id, 'name' => "Route {$school->id}-" . ($r + 1)],
                    [
                        'type' => $r % 2 === 0 ? 'morning' : 'afternoon',
                        'stops' => [
                            ['name' => 'Stop A', 'lat' => 31.2 + ($r * 0.01), 'lng' => 29.9 + ($r * 0.01), 'order' => 1],
                            ['name' => 'School Gate', 'lat' => 31.21, 'lng' => 29.92, 'order' => 2],
                        ],
                        'estimated_minutes' => rand(20, 50),
                        'distance_km' => rand(5, 25),
                        'bus_id' => $buses[$r % $buses->count()]->id,
                        'driver_id' => $drivers[$r % $drivers->count()]->id,
                        'active' => true,
                    ]
                ));
            }

            $parentBatch = 100;
            for ($p = 0; $p < $parentBatch; $p++) {
                $user = User::updateOrCreate(
                    ['email' => "parent.{$school->id}.{$p}@safestep.com"],
                    [
                        'name' => fake()->name(),
                        'password' => Hash::make('password'),
                        'plain_password' => 'password',
                        'role' => 'parent',
                    ]
                );
                $parent = ParentProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    ['phone' => '01' . fake()->numerify('#########'), 'address' => fake()->address(), 'active' => true]
                );

                for ($s = 0; $s < rand(1, 2); $s++) {
                    Student::updateOrCreate(
                        ['parent_id' => $parent->id, 'full_name' => fake()->firstName() . ' ' . fake()->lastName()],
                        [
                            'school_id' => $school->id,
                            'school_name' => $school->name,
                            'grade' => 'Grade ' . rand(1, 12),
                            'bus_id' => $buses->random()->id,
                            'bus_route_id' => $routes->random()->id,
                            'qr_code' => 'QR-' . Str::upper(Str::random(8)),
                            'active' => true,
                        ]
                    );
                }
            }

            for ($day = 0; $day < 14; $day++) {
                $date = now()->subDays($day)->toDateString();
                $trip = Trip::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'driver_id' => $drivers->random()->id,
                        'bus_id' => $buses->random()->id,
                        'bus_route_id' => $routes->random()->id,
                        'trip_date' => $date,
                        'shift' => 'morning',
                    ],
                    ['status' => $day === 0 ? 'active' : 'completed', 'started_at' => now()->subDays($day)]
                );

                Student::where('school_id', $school->id)->inRandomOrder()->limit(20)->get()->each(function (Student $student) use ($trip) {
                    Attendance::updateOrCreate(
                        ['trip_id' => $trip->id, 'student_id' => $student->id],
                        ['status' => fake()->randomElement(['picked_up', 'dropped_off', 'absent'])]
                    );
                });
            }

            if ($index % 10 === 0) {
                EmergencyAlert::updateOrCreate(
                    ['school_id' => $school->id, 'type' => 'weather', 'message' => 'Demo weather alert for ' . $school->name],
                    ['severity' => 'medium', 'status' => 'open']
                );
            }
        }

        $this->command?->info('Large-scale seed complete: 50 schools, drivers, buses, routes, parents, students, trips, attendance.');
    }
}
