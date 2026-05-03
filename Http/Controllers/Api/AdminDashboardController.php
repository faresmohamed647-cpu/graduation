<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function stats(Request $request)
    {
        $today = CarbonImmutable::today();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_parents'     => ParentProfile::count(),
                'total_drivers'     => Driver::count(),
                'total_students'    => Student::count(),
                'total_buses'       => Bus::count(),
                'active_buses'      => Bus::where('active', true)->count(),
                'active_trips'      => Trip::where('status', 'active')->count(),
                'today_trips'       => Trip::whereDate('trip_date', $today)->count(),
                'total_routes'      => BusRoute::count(),
                'total_users'       => User::count(),
                'pending_requests'  => 0,
                'complaints_today'  => 0,
            ],
        ]);
    }

    public function attendanceSummary(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $trips = Trip::whereDate('trip_date', $date)->with('attendance')->get();

        $totalStudents = 0;
        $pickedUp = 0;
        $droppedOff = 0;
        $absent = 0;

        foreach ($trips as $trip) {
            foreach ($trip->attendance as $a) {
                $totalStudents++;
                match ($a->status) {
                    'picked_up'   => $pickedUp++,
                    'dropped_off' => $droppedOff++,
                    default       => $absent++,
                };
            }
        }

        return response()->json([
            'success' => true,
            'data'    => compact('totalStudents', 'pickedUp', 'droppedOff', 'absent'),
        ]);
    }

    public function tripsOverview(Request $request)
    {
        $days = (int) $request->get('days', 7);
        $from = CarbonImmutable::today()->subDays($days);

        $trips = Trip::where('trip_date', '>=', $from)
            ->selectRaw("trip_date, status, COUNT(*) as count")
            ->groupBy('trip_date', 'status')
            ->get();

        return response()->json(['success' => true, 'data' => $trips]);
    }

    public function fleetStatus(Request $request)
    {
        $buses = Bus::withCount('trips')->get()->map(fn (Bus $b) => [
            'id'          => $b->id,
            'bus_number'  => $b->bus_number,
            'plate'       => $b->plate_number,
            'capacity'    => $b->capacity,
            'active'      => $b->active,
            'total_trips' => $b->trips_count,
        ]);

        return response()->json(['success' => true, 'data' => $buses]);
    }
}
