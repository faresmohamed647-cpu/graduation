<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Attendance;
use App\Models\Bus;
use App\Models\BusLocation;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\EmergencyAlert;
use App\Models\School;
use App\Services\BusTrackingService;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = $this->seedUser('Admin SafeStep', 'admin@safestep.com', 'admin');

        $schools = $this->seedSchools();
        $this->seedSchoolAdmins($schools);

        $drivers = collect([
            ['name' => 'Ahmed Khaled', 'email' => 'ahmed.khaled@safestep.com', 'phone' => '01012345678', 'license' => 'DR-1234-EG', 'exp' => 8],
            ['name' => 'Mohamed Samir', 'email' => 'mohamed.samir@safestep.com', 'phone' => '01098765432', 'license' => 'DR-5678-EG', 'exp' => 5],
            ['name' => 'Hassan Ibrahim', 'email' => 'hassan.ibrahim@safestep.com', 'phone' => '01155443322', 'license' => 'DR-9012-EG', 'exp' => 12],
        ])->map(function (array $data) {
            $user = $this->seedUser($data['name'], $data['email'], 'driver');

            return Driver::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $data['phone'],
                    'license_number' => $data['license'],
                    'years_experience' => $data['exp'],
                    'active' => true,
                    'status' => 'approved',
                ]
            );
        })->values();

        $parents = collect();
        $students = collect();

        foreach ($this->parentSeedData() as $data) {
            $user = $this->seedUser($data['name'], $data['email'], 'parent');
            $parent = ParentProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['phone' => $data['phone'], 'address' => $data['address'], 'active' => true]
            );
            $parents->push($parent);

            foreach ($data['children'] as $child) {
                $school = $schools->first(fn (School $s) => $s->name === $child['school']);
                $students->push(Student::updateOrCreate(
                    ['parent_id' => $parent->id, 'full_name' => $child['full_name']],
                    [
                        'grade' => $child['grade'],
                        'school_name' => $child['school'],
                        'school_id' => $school?->id,
                        'active' => true,
                    ]
                ));
            }
        }

        $primarySchool = $schools->first();

        $buses = Bus::exists()
            ? Bus::orderBy('id')->take(4)->get()->values()
            : collect([
                ['bus_number' => 'BUS-001', 'plate_number' => 'ABC 1234', 'capacity' => 45],
                ['bus_number' => 'BUS-002', 'plate_number' => 'DEF 5678', 'capacity' => 40],
                ['bus_number' => 'BUS-003', 'plate_number' => 'GHI 9012', 'capacity' => 50],
                ['bus_number' => 'BUS-004', 'plate_number' => 'JKL 3456', 'capacity' => 35],
            ])->map(fn (array $data, int $index) => Bus::create($data + [
                'active' => true,
                'school_id' => $primarySchool?->id,
                'driver_id' => $drivers[$index % $drivers->count()]->id ?? null,
                'status' => 'active',
            ]))->values();

        $routes = BusRoute::exists()
            ? BusRoute::orderBy('id')->take(4)->get()->values()
            : collect($this->routeSeedData())->map(fn (array $data, int $index) => BusRoute::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'stops' => $data['stops'],
                'estimated_minutes' => $data['estimated_minutes'],
                'distance_km' => ($data['estimated_minutes'] ?? 30) * 0.5,
                'school_id' => $primarySchool?->id,
                'bus_id' => $buses[$index % $buses->count()]->id ?? null,
                'driver_id' => $drivers[$index % $drivers->count()]->id ?? null,
                'active' => true,
            ]))->values();

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $trips = Trip::exists()
            ? Trip::orderBy('id')->take(6)->get()->values()
            : collect([
                ['driver_id' => $drivers[0]->id, 'bus_id' => $buses[0]->id, 'bus_route_id' => $routes[0]->id, 'trip_date' => $today, 'shift' => 'morning', 'status' => 'assigned'],
                ['driver_id' => $drivers[1]->id, 'bus_id' => $buses[1]->id, 'bus_route_id' => $routes[1]->id, 'trip_date' => $today, 'shift' => 'morning', 'status' => 'active', 'started_at' => now()->subMinutes(30)],
                ['driver_id' => $drivers[2]->id, 'bus_id' => $buses[2]->id, 'bus_route_id' => $routes[2]->id, 'trip_date' => $today, 'shift' => 'afternoon', 'status' => 'assigned'],
                ['driver_id' => $drivers[0]->id, 'bus_id' => $buses[3]->id, 'bus_route_id' => $routes[3]->id, 'trip_date' => $today, 'shift' => 'afternoon', 'status' => 'active', 'started_at' => now()->subMinutes(15)],
                ['driver_id' => $drivers[1]->id, 'bus_id' => $buses[0]->id, 'bus_route_id' => $routes[0]->id, 'trip_date' => $yesterday, 'shift' => 'morning', 'status' => 'completed', 'started_at' => now()->subDay()->setTime(7, 0), 'ended_at' => now()->subDay()->setTime(8, 30)],
                ['driver_id' => $drivers[2]->id, 'bus_id' => $buses[1]->id, 'bus_route_id' => $routes[1]->id, 'trip_date' => $yesterday, 'shift' => 'afternoon', 'status' => 'completed', 'started_at' => now()->subDay()->setTime(14, 0), 'ended_at' => now()->subDay()->setTime(15, 30)],
            ])->map(fn (array $data) => Trip::create($data + ['school_id' => $primarySchool?->id]))->values();

        $this->syncSchoolFleet($schools, $drivers, $students, $buses, $routes, $trips);

        if (! Attendance::exists()) {
            $this->seedAttendance($trips, $students);
        }

        $this->seedAlexandriaRoutes($routes);
        $this->syncDemoTripsForToday($trips);
        $this->seedBusLocations($trips, app(BusTrackingService::class));

        $this->seedNotifications($admin, $drivers, $parents);
        $this->seedApplications();
        $schools->each(fn (School $school) => $this->seedEmergencyAlerts($school));

        if (env('SEED_LARGE_SCALE', false)) {
            $this->call(LargeScaleSeeder::class);
        }

        $this->command?->info('SafeStep seed data is present. Seeder is idempotent and safe to rerun.');
        $this->command?->info('School Admin login: school.admin@alazhar.safestep.com / password → /school-admin');
        $this->command?->info('Join registration: /join | Large scale: SEED_LARGE_SCALE=true php artisan db:seed --class=LargeScaleSeeder');
    }

    private function seedApplications(): void
    {
        if (Application::exists()) {
            return;
        }

        $applications = [
            ['full_name' => 'Mohamed Hassan', 'email' => 'mohamed.hassan@safestep.com', 'phone' => '01012349876', 'address' => 'Maadi, Cairo', 'role' => 'parent', 'experience' => 'Father of three children attending Cairo American College. Looking for safe and reliable transportation.', 'notes' => 'meta:{"student_state":"Arab Republic of Egypt","student_relationship":"Father","student_count":3,"student_degree":"Preparatory","student_education_system":"American","school_name":"Cairo American College","school_address":"Maadi, Cairo","school_starting":"7:30 AM"}', 'status' => 'pending'],
            ['full_name' => 'Ahmed Khaled', 'email' => 'ahmed.khaled@safestep.com', 'phone' => '01198765432', 'address' => 'Nasr City, Cairo', 'role' => 'driver', 'experience' => '10 years of professional driving experience including 5 years in school bus transportation. Clean driving record.', 'notes' => 'meta:{"owner_state":"Arab Republic of Egypt","owner_age":38,"owner_gender":"Male","car_type":"Toyota HiAce","car_model":"2023","car_plate":"ABC 9988"}', 'status' => 'accepted'],
            ['full_name' => 'Admin SafeStep', 'email' => 'admin@safestep.com', 'phone' => '01234509876', 'address' => 'Heliopolis, Cairo', 'role' => 'admin', 'experience' => '7 years in school administration and fleet management. Proficient in data analysis.', 'notes' => 'meta:{"admin_department":"Operations","years_experience":7,"highest_qualification":"MBA","availability":"Immediate"}', 'status' => 'reviewed'],
            ['full_name' => 'Fatma Ali', 'email' => 'fatma.ali@safestep.com', 'phone' => '01555112233', 'address' => 'Heliopolis, Cairo', 'role' => 'parent', 'experience' => 'Mother of two. Need safe bus service for school transport.', 'notes' => 'meta:{"student_state":"Arab Republic of Egypt","student_relationship":"Mother","student_count":2,"student_degree":"Primary","student_education_system":"British","school_name":"British International School Cairo","school_address":"Heliopolis, Cairo","school_starting":"8:00 AM"}', 'status' => 'accepted'],
            ['full_name' => 'Mohamed Samir', 'email' => 'mohamed.samir@safestep.com', 'phone' => '01088776655', 'address' => 'Zamalek, Cairo', 'role' => 'driver', 'experience' => '3 years driving minibuses for private schools. First aid certified.', 'notes' => 'meta:{"owner_state":"Arab Republic of Egypt","owner_age":29,"owner_gender":"Male","car_type":"Mercedes Sprinter","car_model":"2022","car_plate":"XYZ 1122"}', 'status' => 'pending'],
            ['full_name' => 'Hana Mostafa', 'email' => 'hana.mostafa@safestep.com', 'phone' => '01277665544', 'address' => 'New Cairo', 'role' => 'parent', 'experience' => 'Mother of two seeking reliable transport for children.', 'notes' => 'meta:{"student_state":"Arab Republic of Egypt","student_relationship":"Mother","student_count":2,"student_degree":"Grade 2","student_education_system":"STEM","school_name":"Maadi STEM School","school_address":"New Cairo","school_starting":"7:45 AM"}', 'status' => 'rejected'],
            ['full_name' => 'Hassan Ibrahim', 'email' => 'hassan.ibrahim@safestep.com', 'phone' => '01166554433', 'address' => 'Sheikh Zayed, Giza', 'role' => 'driver', 'experience' => '15 years commercial driving, heavy vehicle license holder.', 'notes' => 'meta:{"owner_state":"Arab Republic of Egypt","owner_age":45,"owner_gender":"Male","car_type":"Hyundai County","car_model":"2021","car_plate":"DEF 3344"}', 'status' => 'reviewed'],
            ['full_name' => 'Mariam Adel', 'email' => 'amira.khaled@safestep.com', 'phone' => '01033221100', 'address' => 'October City, Giza', 'role' => 'admin', 'experience' => '4 years in IT administration and system management for educational institutions.', 'notes' => 'meta:{"admin_department":"IT & Systems","years_experience":4,"highest_qualification":"BSc Computer Science","availability":"Within 2 Weeks"}', 'status' => 'pending'],
        ];

        foreach ($applications as $app) {
            $user = User::where('email', $app['email'])->first();
            Application::create(array_merge($app, [
                'user_id' => $user?->id,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]));
        }
    }

    private function seedUser(string $name, string $email, string $role, ?int $schoolId = null): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->fill([
            'name' => $name,
            'password' => $user->exists ? $user->password : Hash::make('password'),
            'plain_password' => 'password',
            'role' => $role,
            'school_id' => $schoolId,
        ]);
        $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()]);
        $user->save();

        return $user;
    }

    private function seedSchools()
    {
        $data = [
            [
                'name' => 'Al-Azhar International School',
                'email' => 'admin@alazhar.safestep.com',
                'phone' => '03-1234567',
                'address' => 'Smouha, Alexandria',
                'principal_name' => 'Dr. Nadia Hassan',
                'status' => 'active',
            ],
            [
                'name' => 'Cairo American College',
                'email' => 'admin@cac.safestep.com',
                'phone' => '02-9876543',
                'address' => 'Maadi, Cairo',
                'principal_name' => 'Mr. James Wilson',
                'status' => 'active',
            ],
            [
                'name' => 'British International School Cairo',
                'email' => 'admin@bisc.safestep.com',
                'phone' => '02-5551234',
                'address' => 'Heliopolis, Cairo',
                'principal_name' => 'Mrs. Sarah Thompson',
                'status' => 'active',
            ],
        ];

        return collect($data)->map(fn (array $school) => School::updateOrCreate(
            ['name' => $school['name']],
            $school
        ))->values();
    }

    private function seedSchoolAdmins($schools): void
    {
        $admins = [
            ['name' => 'Nadia Hassan', 'email' => 'school.admin@alazhar.safestep.com', 'school' => 'Al-Azhar International School'],
            ['name' => 'James Wilson', 'email' => 'school.admin@cac.safestep.com', 'school' => 'Cairo American College'],
            ['name' => 'Sarah Thompson', 'email' => 'school.admin@bisc.safestep.com', 'school' => 'British International School Cairo'],
        ];

        foreach ($admins as $admin) {
            $school = $schools->first(fn (School $s) => $s->name === $admin['school']);
            $this->seedUser($admin['name'], $admin['email'], 'school_admin', $school?->id);
        }
    }

    private function seedEmergencyAlerts(?School $school): void
    {
        if (! $school || EmergencyAlert::where('school_id', $school->id)->exists()) {
            return;
        }

        $messages = [
            'Al-Azhar International School' => 'Heavy fog reported on Smouha route — delays expected.',
            'Cairo American College' => 'Traffic congestion near Maadi — afternoon pickup delayed.',
            'British International School Cairo' => 'Road maintenance on Heliopolis corridor — monitor trip times.',
        ];

        EmergencyAlert::create([
            'school_id' => $school->id,
            'type' => 'weather',
            'severity' => 'medium',
            'status' => 'open',
            'message' => $messages[$school->name] ?? 'Operational alert for school transport.',
        ]);
    }

    private function syncSchoolFleet($schools, $drivers, $students, $buses, $routes, $trips): void
    {
        $plan = [
            'Al-Azhar International School' => [
                'driver_email' => 'ahmed.khaled@safestep.com',
                'bus_numbers' => ['BUS-001', 'BUS-002'],
                'route_index' => 0,
            ],
            'Cairo American College' => [
                'driver_email' => 'mohamed.samir@safestep.com',
                'bus_numbers' => ['BUS-003'],
                'route_index' => 1,
            ],
            'British International School Cairo' => [
                'driver_email' => 'hassan.ibrahim@safestep.com',
                'bus_numbers' => ['BUS-004'],
                'route_index' => 2,
            ],
        ];

        foreach ($plan as $schoolName => $config) {
            $school = $schools->first(fn (School $s) => $s->name === $schoolName);
            if (! $school) {
                continue;
            }

            $driver = Driver::with('user')->whereHas('user', fn ($q) => $q->where('email', $config['driver_email']))->first()
                ?? $drivers->first();
            if ($driver) {
                $driver->update(['school_id' => $school->id, 'active' => true, 'status' => 'approved']);
            }

            $schoolBuses = Bus::whereIn('bus_number', $config['bus_numbers'])->get();
            foreach ($schoolBuses as $bus) {
                $bus->update([
                    'school_id' => $school->id,
                    'driver_id' => $driver?->id,
                    'active' => true,
                    'status' => 'active',
                ]);
            }

            $route = $routes->values()[$config['route_index']] ?? null;
            if ($route) {
                $route->update([
                    'school_id' => $school->id,
                    'bus_id' => $schoolBuses->first()?->id,
                    'driver_id' => $driver?->id,
                    'active' => true,
                ]);
            }

            $schoolStudents = Student::where('school_id', $school->id)->get();
            foreach ($schoolStudents->values() as $index => $student) {
                $bus = $schoolBuses[$index % max(1, $schoolBuses->count())] ?? $schoolBuses->first();
                $student->update([
                    'bus_id' => $bus?->id,
                    'bus_route_id' => $route?->id,
                    'school_name' => $school->name,
                    'active' => true,
                ]);
            }

            if ($schoolBuses->isNotEmpty()) {
                Trip::whereIn('bus_id', $schoolBuses->pluck('id'))->update(['school_id' => $school->id]);
            }
        }

        Student::whereNull('school_id')->whereNotNull('school_name')->get()->each(function (Student $student) use ($schools) {
            $school = $schools->first(fn (School $s) => $s->name === $student->school_name);
            if ($school) {
                $student->update(['school_id' => $school->id]);
            }
        });
    }

    private function seedAttendance($trips, $students): void
    {
        $sets = [
            [$trips[1], $students->slice(0, 4), ['status' => 'picked_up', 'picked_up_at' => now()->subMinutes(20)]],
            [$trips[0], $students->slice(4, 3), ['status' => 'absent']],
            [$trips[4], $students->slice(0, 5), ['status' => 'dropped_off', 'picked_up_at' => now()->subDay()->setTime(7, 15), 'dropped_off_at' => now()->subDay()->setTime(8, 20)]],
            [$trips[3], $students->slice(7, 3), ['status' => 'picked_up', 'picked_up_at' => now()->subMinutes(10)]],
        ];

        foreach ($sets as [$trip, $tripStudents, $values]) {
            foreach ($tripStudents as $student) {
                Attendance::updateOrCreate(
                    ['trip_id' => $trip->id, 'student_id' => $student->id],
                    $values
                );
            }
        }
    }

    private function seedNotifications(User $admin, $drivers, $parents): void
    {
        $notification = fn (string $title, string $body) => new class($title, $body) extends Notification {
            public function __construct(private readonly string $title, private readonly string $body) {}
            public function via($notifiable): array { return ['database']; }
            public function toArray($notifiable): array { return ['title' => $this->title, 'body' => $this->body]; }
        };

        foreach ($parents as $parent) {
            $this->notifyOnce($parent->user, 'Welcome to SafeStep!', 'Your children have been registered successfully. You will receive real-time updates about their bus trips.', $notification);
            $this->notifyOnce($parent->user, 'Trip Update', 'Your child has been picked up and is on the way to school.', $notification);
        }

        foreach ($drivers as $driver) {
            $this->notifyOnce($driver->user, 'New Trip Assigned', 'You have a new trip assigned for today. Please check your dashboard.', $notification);
        }

        $this->notifyOnce($admin, 'System Ready', 'SafeStep Bus Management System has been initialized with sample data.', $notification);
    }

    private function notifyOnce(User $user, string $title, string $body, callable $notification): void
    {
        $exists = $user->notifications()
            ->where('data', 'like', '%' . addcslashes($title, '%_') . '%')
            ->exists();

        if (! $exists) {
            $user->notify($notification($title, $body));
        }
    }

    private function parentSeedData(): array
    {
        return [
            ['name' => 'Sara Ahmed', 'email' => 'sara.ahmed@safestep.com', 'phone' => '01234567890', 'address' => 'Nasr City, Cairo', 'children' => [
                ['full_name' => 'Youssef Ahmed', 'grade' => 'Grade 3', 'school' => 'Al-Azhar International School'],
                ['full_name' => 'Malak Ahmed', 'grade' => 'Grade 5', 'school' => 'Al-Azhar International School'],
            ]],
            ['name' => 'Mohamed Hassan', 'email' => 'mohamed.hassan@safestep.com', 'phone' => '01122334455', 'address' => 'Maadi, Cairo', 'children' => [
                ['full_name' => 'Omar Hassan', 'grade' => 'Grade 7', 'school' => 'Cairo American College'],
                ['full_name' => 'Nour Hassan', 'grade' => 'Grade 4', 'school' => 'Cairo American College'],
                ['full_name' => 'Adam Hassan', 'grade' => 'Grade 1', 'school' => 'Cairo American College'],
            ]],
            ['name' => 'Fatma Ali', 'email' => 'fatma.ali@safestep.com', 'phone' => '01011223344', 'address' => 'Heliopolis, Cairo', 'children' => [
                ['full_name' => 'Ali Mohamed', 'grade' => 'Grade 6', 'school' => 'British International School Cairo'],
                ['full_name' => 'Farida Mohamed', 'grade' => 'Grade 9', 'school' => 'British International School Cairo'],
            ]],
            ['name' => 'Hana Mostafa', 'email' => 'hana.mostafa@safestep.com', 'phone' => '01288776655', 'address' => 'New Cairo', 'children' => [
                ['full_name' => 'Ziad Mostafa', 'grade' => 'Grade 2', 'school' => 'Cairo American College'],
                ['full_name' => 'Salma Mostafa', 'grade' => 'Grade 8', 'school' => 'Cairo American College'],
            ]],
            ['name' => 'Amira Khaled', 'email' => 'amira.khaled@safestep.com', 'phone' => '01555667788', 'address' => 'October City, Giza', 'children' => [
                ['full_name' => 'Hamza Khaled', 'grade' => 'Grade 10', 'school' => 'British International School Cairo'],
                ['full_name' => 'Jana Khaled', 'grade' => 'Grade 4', 'school' => 'British International School Cairo'],
                ['full_name' => 'Kareem Khaled', 'grade' => 'Grade 6', 'school' => 'British International School Cairo'],
            ]],
        ];
    }

    private function routeSeedData(): array
    {
        return [
            ['name' => 'Smouha to Sidi Gaber', 'type' => 'morning', 'estimated_minutes' => 35, 'stops' => [
                ['name' => 'Smouha Club', 'lat' => 31.2156, 'lng' => 29.9553, 'order' => 1],
                ['name' => 'Sporting', 'lat' => 31.2089, 'lng' => 29.9389, 'order' => 2],
                ['name' => 'Sidi Gaber', 'lat' => 31.2187, 'lng' => 29.9422, 'order' => 3],
                ['name' => 'Raml Station', 'lat' => 31.2001, 'lng' => 29.9187, 'order' => 4],
            ]],
            ['name' => 'Sidi Bishr to Fleming', 'type' => 'morning', 'estimated_minutes' => 40, 'stops' => [
                ['name' => 'Sidi Bishr', 'lat' => 31.2550, 'lng' => 29.9980, 'order' => 1],
                ['name' => 'Mandara', 'lat' => 31.2700, 'lng' => 30.0200, 'order' => 2],
                ['name' => 'Fleming', 'lat' => 31.2400, 'lng' => 29.9700, 'order' => 3],
            ]],
            ['name' => 'Sporting to Stanley', 'type' => 'afternoon', 'estimated_minutes' => 30, 'stops' => [
                ['name' => 'Sporting', 'lat' => 31.2089, 'lng' => 29.9389, 'order' => 1],
                ['name' => 'Cleopatra', 'lat' => 31.2280, 'lng' => 29.9480, 'order' => 2],
                ['name' => 'Stanley', 'lat' => 31.2380, 'lng' => 29.9620, 'order' => 3],
            ]],
            ['name' => 'Agami to Mansheya', 'type' => 'morning', 'estimated_minutes' => 50, 'stops' => [
                ['name' => 'Agami', 'lat' => 31.0950, 'lng' => 29.7600, 'order' => 1],
                ['name' => 'Miami', 'lat' => 31.2800, 'lng' => 30.0100, 'order' => 2],
                ['name' => 'Mansheya', 'lat' => 31.1980, 'lng' => 29.8950, 'order' => 3],
            ]],
        ];
    }

    private function seedAlexandriaRoutes($routes): void
    {
        $alexRoutes = $this->routeSeedData();

        foreach ($routes->values() as $index => $route) {
            if (! isset($alexRoutes[$index])) {
                break;
            }

            $route->update([
                'name' => $alexRoutes[$index]['name'],
                'type' => $alexRoutes[$index]['type'],
                'stops' => $alexRoutes[$index]['stops'],
                'estimated_minutes' => $alexRoutes[$index]['estimated_minutes'],
            ]);
        }
    }

    private function syncDemoTripsForToday($trips): void
    {
        $today = now()->toDateString();

        foreach ($trips->take(4) as $trip) {
            $trip->update(['trip_date' => $today]);
        }
    }

    private function seedBusLocations($trips, BusTrackingService $tracking): void
    {
        $alexPositions = [
            ['lat' => 31.2156, 'lng' => 29.9553, 'speed' => 38,  'name' => 'Smouha'],
            ['lat' => 31.2550, 'lng' => 29.9980, 'speed' => 0,   'name' => 'Sidi Bishr'],
            ['lat' => 31.2089, 'lng' => 29.9389, 'speed' => 32,  'name' => 'Sporting'],
            ['lat' => 31.2001, 'lng' => 29.9187, 'speed' => 28,  'name' => 'Raml Station'],
            ['lat' => 31.2380, 'lng' => 29.9620, 'speed' => 0,   'name' => 'Stanley'],
            ['lat' => 31.2400, 'lng' => 29.9700, 'speed' => 35,  'name' => 'Fleming'],
            ['lat' => 31.2280, 'lng' => 29.9480, 'speed' => 22,  'name' => 'Cleopatra'],
            ['lat' => 31.1980, 'lng' => 29.8950, 'speed' => 25,  'name' => 'Mansheya'],
        ];

        $tripsByBus = Trip::whereDate('trip_date', now()->toDateString())
            ->orderByDesc('id')
            ->get()
            ->unique('bus_id')
            ->keyBy('bus_id');

        Bus::where('active', true)->orderBy('id')->get()->each(function (Bus $bus, int $index) use ($alexPositions, $tracking, $tripsByBus) {
            $position = $alexPositions[$index % count($alexPositions)];
            $trip = $tripsByBus->get($bus->id);

            try {
                $tracking->updateLocation(
                    $bus,
                    $trip,
                    $position['lat'],
                    $position['lng'],
                    $position['speed'],
                );
            } catch (\DomainException) {
                // skip invalid demo coordinates
            }
        });
    }
}
