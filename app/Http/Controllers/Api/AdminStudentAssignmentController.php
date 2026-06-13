<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\Student;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminStudentAssignmentController extends Controller
{
    public function index()
    {
        $students = Student::with(['parent.user', 'bus', 'route'])
            ->where('assignment_status', 'pending')
            ->orWhereNull('bus_id')
            ->latest('id')
            ->get()
            ->groupBy('parent_id')
            ->map(function ($items) {
                $parent = $items->first()->parent;

                return [
                    'parent_id' => $parent?->id,
                    'parent_name' => $parent?->user?->name ?? 'Parent',
                    'parent_phone' => $parent?->phone,
                    'parent_email' => $parent?->user?->email,
                    'students' => $items->map(fn (Student $student) => $this->mapStudent($student))->values(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'groups' => $students,
                'drivers' => Driver::with('user')->where('active', true)->orderBy('id')->get()
                    ->map(fn (Driver $driver) => [
                        'id' => $driver->id,
                        'name' => $driver->user?->name ?? $driver->full_name ?? 'Driver',
                        'phone' => $driver->phone,
                    ]),
                'buses' => Bus::orderBy('bus_number')->get(['id', 'bus_number', 'capacity', 'driver_id', 'bus_route_id']),
                'routes' => BusRoute::orderBy('name')->get(['id', 'name', 'type', 'bus_id', 'driver_id']),
            ],
        ]);
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'bus_route_id' => ['nullable', 'integer', 'exists:bus_routes,id'],
            'shift' => ['nullable', Rule::in(['morning', 'afternoon'])],
            'trip_date' => ['nullable', 'date'],
        ]);

        $driver = Driver::findOrFail($data['driver_id']);
        $bus = Bus::findOrFail($data['bus_id']);
        $route = ! empty($data['bus_route_id'])
            ? BusRoute::findOrFail($data['bus_route_id'])
            : BusRoute::firstOrCreate(
                ['name' => 'Route for ' . $bus->bus_number],
                ['type' => $data['shift'] ?? 'morning', 'active' => true]
            );

        $tripDate = CarbonImmutable::parse($data['trip_date'] ?? CarbonImmutable::today()->toDateString())->toDateString();
        $shift = $data['shift'] ?? 'morning';

        $students = Student::whereIn('id', $data['student_ids'])->get();

        DB::transaction(function () use ($students, $driver, $bus, $route, $tripDate, $shift) {
            $bus->update([
                'driver_id' => $driver->id,
                'bus_route_id' => $route->id,
                'school_id' => $driver->school_id ?? $bus->school_id,
            ]);

            $route->update([
                'driver_id' => $driver->id,
                'bus_id' => $bus->id,
                'school_id' => $driver->school_id ?? $route->school_id,
            ]);

            $students->each(function (Student $student) use ($driver, $bus, $route) {
                $student->update([
                    'school_id' => $student->school_id ?? $driver->school_id,
                    'bus_id' => $bus->id,
                    'bus_route_id' => $route->id,
                    'assignment_status' => 'assigned',
                    'active' => true,
                ]);
            });

            $trip = Trip::firstOrCreate(
                [
                    'driver_id' => $driver->id,
                    'bus_id' => $bus->id,
                    'bus_route_id' => $route->id,
                    'trip_date' => $tripDate,
                    'shift' => $shift,
                ],
                [
                    'school_id' => $driver->school_id,
                    'status' => 'assigned',
                    'meta' => ['source' => 'admin_child_assignment'],
                ]
            );

            foreach ($students as $student) {
                Attendance::firstOrCreate(
                    ['trip_id' => $trip->id, 'student_id' => $student->id],
                    ['status' => 'absent']
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Students assigned successfully.',
        ]);
    }

    private function mapStudent(Student $student): array
    {
        return [
            'id' => $student->id,
            'full_name' => $student->full_name,
            'age' => $student->age,
            'grade' => $student->grade,
            'school_name' => $student->school_name,
            'pickup_location' => $student->pickup_location,
            'dropoff_location' => $student->dropoff_location,
            'pickup_time' => $student->pickup_time,
            'dropoff_time' => $student->dropoff_time,
            'has_medical_condition' => $student->has_medical_condition,
            'medical_condition' => $student->medical_condition,
            'medication' => $student->medication,
            'assignment_status' => $student->assignment_status,
        ];
    }
}
