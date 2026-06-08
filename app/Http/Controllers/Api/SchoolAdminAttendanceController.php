<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class SchoolAdminAttendanceController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $date = $request->get('date', now()->toDateString());

        $records = Attendance::with(['student', 'trip.bus', 'trip.route'])
            ->whereHas('trip', fn ($q) => $q->where('school_id', $schoolId)->whereDate('trip_date', $date))
            ->latest('id')
            ->get()
            ->map(fn (Attendance $a) => [
                'id' => $a->id,
                'student_name' => $a->student?->full_name,
                'trip_id' => $a->trip_id,
                'bus_number' => $a->trip?->bus?->bus_number,
                'route_name' => $a->trip?->route?->name,
                'status' => $a->status,
                'picked_up_at' => $a->picked_up_at,
                'dropped_off_at' => $a->dropped_off_at,
            ]);

        return response()->json(['success' => true, 'data' => $records]);
    }

    public function reports(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to = $request->get('to', now()->toDateString());

        $summary = Attendance::query()
            ->selectRaw('attendance.status, COUNT(*) as count')
            ->join('trips', 'attendance.trip_id', '=', 'trips.id')
            ->where('trips.school_id', $schoolId)
            ->whereBetween('trips.trip_date', [$from, $to])
            ->groupBy('attendance.status')
            ->get();

        return response()->json(['success' => true, 'data' => $summary]);
    }
}
