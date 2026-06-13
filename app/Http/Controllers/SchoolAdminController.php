<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Driver;
use App\Models\EmergencyAlert;
use App\Models\Student;
use App\Models\Trip;
use Carbon\CarbonImmutable;

class SchoolAdminController extends Controller
{
    public function dashboard(string $initialPage = 'dashboard')
    {
        $user = auth()->user();
        $school = $user->school;
        abort_unless($school, 403, 'School administrator is not linked to a school.');

        $schoolId = $school->id;
        $today = CarbonImmutable::today();

        $stats = [
            'school_name' => $school->name,
            'students_count' => Student::where('school_id', $schoolId)->count(),
            'active_students' => Student::where('school_id', $schoolId)->where('active', true)->count(),
            'drivers_count' => Driver::where('school_id', $schoolId)->count(),
            'buses_count' => Bus::where('school_id', $schoolId)->count(),
            'buses_active' => Bus::where('school_id', $schoolId)->where('active', true)->count(),
            'trips_today' => Trip::where('school_id', $schoolId)->whereDate('trip_date', $today)->count(),
            'trips_active' => Trip::where('school_id', $schoolId)->where('status', 'active')->count(),
            'emergency_alerts' => EmergencyAlert::where('school_id', $schoolId)->where('status', 'open')->count(),
        ];

        $school->loadCount(['students', 'drivers', 'buses']);

        $isApproved = $school->status === 'active';
        $appStatus = $school->status ?: 'pending_details';

        return view('school-admin.school-admin', [
            'stats' => $stats,
            'initialPage' => $initialPage,
            'school' => $school,
            'user' => $user,
            'isApproved' => $isApproved,
            'appStatus' => $appStatus,
        ]);
    }

    public function section(string $section)
    {
        $allowed = [
            'dashboard',
            'parents',
            'students',
            'buses',
            'drivers',
            'routes',
            'trips',
            'attendance',
            'notifications',
            'emergency',
            'reports',
            'tracking',
            'settings',
            'activity-logs',
        ];

        abort_unless(in_array($section, $allowed, true), 404);

        return $this->dashboard($section);
    }
}
