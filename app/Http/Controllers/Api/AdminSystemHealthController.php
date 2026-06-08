<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyAlert;
use App\Models\School;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminSystemHealthController extends Controller
{
    public function health(Request $request)
    {
        $data = Cache::remember('admin_system_health', 30, function () {
            $dbOk = true;
            try {
                DB::select('SELECT 1');
            } catch (\Throwable) {
                $dbOk = false;
            }

            return [
                'database' => $dbOk ? 'healthy' : 'error',
                'cache' => Cache::store()->put('health_ping', true, 10) ? 'healthy' : 'degraded',
                'storage_writable' => is_writable(storage_path('app')),
                'queue_connection' => config('queue.default'),
                'failed_jobs' => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0,
                'pending_jobs' => Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0,
                'users_total' => User::count(),
                'schools_total' => School::count(),
                'students_total' => Student::count(),
                'open_emergencies' => EmergencyAlert::where('status', 'open')->count(),
                'active_trips' => Trip::where('status', 'active')->count(),
                'server_time' => now()->toIso8601String(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function schoolPerformance(Request $request)
    {
        $schools = School::withCount(['students', 'drivers', 'buses', 'trips'])
            ->with(['emergencyAlerts' => fn ($q) => $q->where('status', 'open')])
            ->get()
            ->map(fn (School $school) => [
                'id' => $school->id,
                'name' => $school->name,
                'status' => $school->status,
                'students' => $school->students_count,
                'drivers' => $school->drivers_count,
                'buses' => $school->buses_count,
                'trips' => $school->trips_count,
                'open_emergencies' => $school->emergencyAlerts->count(),
                'attendance_rate' => $this->attendanceRateForSchool($school->id),
            ]);

        return response()->json(['success' => true, 'data' => $schools]);
    }

    public function emergencyOverview()
    {
        $alerts = EmergencyAlert::with(['school:id,name', 'driver.user:id,name', 'student:id,full_name'])
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'open' => EmergencyAlert::where('status', 'open')->count(),
                'resolved' => EmergencyAlert::where('status', 'resolved')->count(),
                'recent' => $alerts,
            ],
        ]);
    }

    private function attendanceRateForSchool(int $schoolId): float
    {
        $total = DB::table('attendance')
            ->join('trips', 'attendance.trip_id', '=', 'trips.id')
            ->where('trips.school_id', $schoolId)
            ->count();

        if ($total === 0) {
            return 100.0;
        }

        $present = DB::table('attendance')
            ->join('trips', 'attendance.trip_id', '=', 'trips.id')
            ->where('trips.school_id', $schoolId)
            ->whereIn('attendance.status', ['picked_up', 'dropped_off'])
            ->count();

        return round(($present / $total) * 100, 1);
    }
}
