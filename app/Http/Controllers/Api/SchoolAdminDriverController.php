<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Trip;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SchoolAdminDriverController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $drivers = Driver::where('school_id', $schoolId)
            ->with(['user', 'buses'])
            ->latest('id')
            ->get()
            ->map(fn (Driver $driver) => $this->mapDriver($driver));

        return response()->json(['success' => true, 'data' => $drivers]);
    }

    public function show(Request $request, Driver $driver)
    {
        $this->authorizeSchoolDriver($request, $driver);
        $driver->load(['user', 'buses.route', 'trips' => fn ($q) => $q->latest()->limit(20)]);

        $metrics = [
            'total_trips' => $driver->trips()->count(),
            'completed_trips' => $driver->trips()->where('status', 'completed')->count(),
            'active_trips' => $driver->trips()->where('status', 'active')->count(),
            'on_time_rate' => $this->onTimeRate($driver),
        ];

        return response()->json([
            'success' => true,
            'data' => array_merge($this->mapDriver($driver), ['metrics' => $metrics, 'trip_history' => $driver->trips]),
        ]);
    }

    public function update(Request $request, Driver $driver, ActivityLogService $logger)
    {
        $this->authorizeSchoolDriver($request, $driver);
        $data = $request->validate([
            'phone' => ['sometimes', 'string', 'max:30'],
            'license_number' => ['sometimes', 'string', 'max:100'],
            'years_experience' => ['sometimes', 'integer', 'min:0'],
            'active' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'in:approved,pending,rejected,interview_scheduled'],
        ]);

        $driver->update($data);
        $logger->log($request, 'driver.updated', $driver);

        return response()->json(['success' => true, 'data' => $this->mapDriver($driver->fresh('user')), 'message' => 'Driver updated']);
    }

    private function authorizeSchoolDriver(Request $request, Driver $driver): void
    {
        abort_unless((int) $driver->school_id === $this->schoolId($request), 403);
    }

    private function onTimeRate(Driver $driver): float
    {
        $completed = Trip::where('driver_id', $driver->id)->where('status', 'completed')->count();
        if ($completed === 0) {
            return 100.0;
        }

        $onTime = Trip::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereNotNull('ended_at')
            ->count();

        return round(($onTime / $completed) * 100, 1);
    }

    private function mapDriver(Driver $driver): array
    {
        return [
            'id' => $driver->id,
            'name' => $driver->user?->name ?? $driver->full_name,
            'email' => $driver->user?->email,
            'phone' => $driver->phone,
            'license' => $driver->license_number,
            'license_number' => $driver->license_number,
            'years_experience' => $driver->years_experience,
            'experience' => ($driver->years_experience ?? 0) . ' years',
            'active' => $driver->active,
            'status' => $driver->status ?? ($driver->active ? 'active' : 'inactive'),
            'bus' => $driver->buses->first()?->bus_number,
            'created_at' => $driver->created_at,
        ];
    }
}
