<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\EmergencyAlert;
use App\Models\Student;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Cache;

class SchoolAdminDashboardController extends Controller
{
    use ResolvesSchoolScope;

    public function stats(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $today = CarbonImmutable::today();
        $cacheKey = "school_admin_stats_{$schoolId}_{$today->toDateString()}";

        $data = Cache::remember($cacheKey, 60, function () use ($schoolId, $today) {
            $studentQuery = Student::where('school_id', $schoolId);
            $tripQuery = Trip::where('school_id', $schoolId);

            $todayAttendance = Attendance::whereHas('trip', fn ($q) => $q->where('school_id', $schoolId))
                ->whereHas('trip', fn ($q) => $q->whereDate('trip_date', $today))
                ->get();

            return [
                'total_students' => (clone $studentQuery)->count(),
                'active_students' => (clone $studentQuery)->where('active', true)->count(),
                'total_drivers' => Driver::where('school_id', $schoolId)->count(),
                'total_buses' => Bus::where('school_id', $schoolId)->count(),
                'active_buses' => Bus::where('school_id', $schoolId)->where('active', true)->count(),
                'active_trips' => (clone $tripQuery)->where('status', 'active')->count(),
                'completed_trips' => (clone $tripQuery)->where('status', 'completed')->whereDate('trip_date', $today)->count(),
                'today_trips' => (clone $tripQuery)->whereDate('trip_date', $today)->count(),
                'today_attendance' => $todayAttendance->whereIn('status', ['picked_up', 'dropped_off'])->count(),
                'today_absence' => $todayAttendance->where('status', 'absent')->count(),
                'emergency_alerts' => EmergencyAlert::where('school_id', $schoolId)->where('status', 'open')->count(),
                'total_routes' => BusRoute::where('school_id', $schoolId)->count(),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function attendanceSummary(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $date = $request->get('date', now()->toDateString());

        $attendance = Attendance::with('student')
            ->whereHas('trip', fn ($q) => $q->where('school_id', $schoolId)->whereDate('trip_date', $date))
            ->get();

        $totalStudents = $attendance->count();
        $pickedUp = $attendance->where('status', 'picked_up')->count();
        $droppedOff = $attendance->where('status', 'dropped_off')->count();
        $absent = $attendance->where('status', 'absent')->count();

        return response()->json([
            'success' => true,
            'data' => compact('totalStudents', 'pickedUp', 'droppedOff', 'absent'),
        ]);
    }

    public function tripsOverview(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $days = (int) $request->get('days', 7);
        $from = CarbonImmutable::today()->subDays($days);

        $trips = Trip::where('school_id', $schoolId)
            ->where('trip_date', '>=', $from)
            ->selectRaw('trip_date, status, COUNT(*) as count')
            ->groupBy('trip_date', 'status')
            ->get();

        return response()->json(['success' => true, 'data' => $trips]);
    }

    public function fleetStatus(Request $request)
    {
        $schoolId = $this->schoolId($request);

        $buses = Bus::where('school_id', $schoolId)
            ->withCount('trips')
            ->with(['driver.user', 'route'])
            ->get()
            ->map(fn (Bus $bus) => [
                'id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'plate' => $bus->plate_number,
                'capacity' => $bus->capacity,
                'active' => $bus->active,
                'status' => $bus->status ?? ($bus->active ? 'active' : 'inactive'),
                'total_trips' => $bus->trips_count,
                'driver' => $bus->driver?->user?->name,
                'route' => $bus->route?->name,
                'insurance_expiry' => $bus->insurance_expiry?->toDateString(),
            ]);

        return response()->json(['success' => true, 'data' => $buses]);
    }

    public function attendanceTrends(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $days = (int) $request->get('days', 30);
        $from = CarbonImmutable::today()->subDays($days);

        $rows = Attendance::query()
            ->selectRaw('DATE(trips.trip_date) as date, attendance.status, COUNT(*) as count')
            ->join('trips', 'attendance.trip_id', '=', 'trips.id')
            ->where('trips.school_id', $schoolId)
            ->where('trips.trip_date', '>=', $from)
            ->groupBy('date', 'attendance.status')
            ->orderBy('date')
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function safetyReports(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $months = (int) $request->get('months', 6);
        $from = CarbonImmutable::today()->subMonths($months);

        $alerts = EmergencyAlert::where('school_id', $schoolId)
            ->where('created_at', '>=', $from)
            ->get()
            ->groupBy(fn (EmergencyAlert $alert) => $alert->created_at->format('Y-m'))
            ->flatMap(function ($items, $month) {
                return $items->groupBy('type')->map(fn ($group, $type) => [
                    'month' => $month,
                    'type' => $type,
                    'count' => $group->count(),
                ]);
            })
            ->sortBy('month')
            ->values();

        return response()->json(['success' => true, 'data' => $alerts]);
    }

    public function kpis(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $today = CarbonImmutable::today();

        $totalStudents = Student::where('school_id', $schoolId)->count();
        $todayAttendance = Attendance::whereHas('trip', fn ($q) => $q->where('school_id', $schoolId)->whereDate('trip_date', $today))->count();
        $todayPresent = Attendance::whereHas('trip', fn ($q) => $q->where('school_id', $schoolId)->whereDate('trip_date', $today))
            ->whereIn('status', ['picked_up', 'dropped_off'])->count();

        return response()->json([
            'success' => true,
            'data' => [
                'attendance_rate' => $todayAttendance > 0 ? round(($todayPresent / $todayAttendance) * 100, 1) : 100,
                'fleet_utilization' => $this->fleetUtilization($schoolId),
                'on_time_trips' => $this->onTimeTripRate($schoolId),
                'safety_score' => max(0, 100 - (EmergencyAlert::where('school_id', $schoolId)->where('status', 'open')->count() * 5)),
                'total_students' => $totalStudents,
            ],
        ]);
    }

    public function studentRisk(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $from = CarbonImmutable::today()->subDays(30);

        $students = Student::where('school_id', $schoolId)->where('active', true)->get();
        $atRisk = [];

        foreach ($students as $student) {
            $records = Attendance::where('student_id', $student->id)
                ->whereHas('trip', fn ($q) => $q->where('school_id', $schoolId)->where('trip_date', '>=', $from))
                ->get();

            if ($records->isEmpty()) {
                continue;
            }

            $absentRate = $records->where('status', 'absent')->count() / $records->count();
            if ($absentRate >= 0.3) {
                $atRisk[] = [
                    'student_id' => $student->id,
                    'name' => $student->full_name,
                    'grade' => $student->grade,
                    'absent_rate' => round($absentRate * 100, 1),
                    'risk_level' => $absentRate >= 0.5 ? 'high' : 'medium',
                ];
            }
        }

        return response()->json(['success' => true, 'data' => $atRisk]);
    }

    private function fleetUtilization(int $schoolId): float
    {
        $buses = Bus::where('school_id', $schoolId)->where('active', true)->count();
        if ($buses === 0) {
            return 0;
        }

        $activeToday = Trip::where('school_id', $schoolId)
            ->whereDate('trip_date', CarbonImmutable::today())
            ->whereIn('status', ['active', 'completed'])
            ->distinct('bus_id')
            ->count('bus_id');

        return round(($activeToday / $buses) * 100, 1);
    }

    private function onTimeTripRate(int $schoolId): float
    {
        $completed = Trip::where('school_id', $schoolId)->where('status', 'completed')->where('trip_date', '>=', CarbonImmutable::today()->subDays(30))->count();
        if ($completed === 0) {
            return 100.0;
        }

        $onTime = Trip::where('school_id', $schoolId)
            ->where('status', 'completed')
            ->where('trip_date', '>=', CarbonImmutable::today()->subDays(30))
            ->whereNotNull('ended_at')
            ->count();

        return round(($onTime / $completed) * 100, 1);
    }

    public function submitDetails(Request $request, ActivityLogService $logger)
    {
        $user = $request->user();
        $school = $user->school;

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School profile not found.',
            ], 404);
        }

        if (! in_array($school->status, ['pending_details', 'rejected'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Profile details were already submitted or approved.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'principal_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'student_count' => ['required', 'integer', 'min:1'],
            'bus_count' => ['required', 'integer', 'min:0'],
            'operating_hours_start' => ['required', 'string', 'max:10'],
            'operating_hours_end' => ['required', 'string', 'max:10'],
            'commercial_register' => ['required', 'string', 'max:100'],
            'license_number' => ['required', 'string', 'max:100'],
            'license_expiry' => ['required', 'date'],
            'license_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'insurance_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'school_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'fleet_type' => ['required', 'string', 'in:own,provided'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $licenseDocPath = $request->file('license_document')->store('schools/documents', 'public');
        $insuranceDocPath = $request->file('insurance_document')->store('schools/documents', 'public');

        $updateData = array_diff_key($validated, array_flip(['license_document', 'insurance_document', 'school_logo']));
        $updateData['license_document_path'] = $licenseDocPath;
        $updateData['insurance_document_path'] = $insuranceDocPath;
        $updateData['status'] = 'pending_approval';
        $updateData['active'] = false;
        $updateData['profile_submitted_at'] = now();

        if ($request->hasFile('school_logo')) {
            $updateData['logo'] = $request->file('school_logo')->store('schools/logos', 'public');
        }

        $school->update($updateData);

        $logger->log($request, 'school_profile_submitted', $school, [
            'fleet_type' => $school->fleet_type,
            'student_count' => $school->student_count,
            'bus_count' => $school->bus_count,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'School profile submitted successfully. Awaiting admin approval.',
            'data' => $school->fresh(),
        ]);
    }

    public function profileStatus(Request $request)
    {
        $school = $request->user()?->school;
        $status = $school?->status ?? 'pending_details';

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $status,
                'is_dashboard_unlocked' => $status === 'active',
            ],
        ]);
    }
}
