<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ServiceRequest;
use App\Models\Trip;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(string $initialAdminPage = 'dashboard')
    {
        $today = CarbonImmutable::today();

        $stats = [
            'users_total' => User::query()->count(),
            'parents_count' => \App\Models\ParentProfile::query()->count(),
            'drivers_count' => \App\Models\Driver::query()->count(),
            'students_count' => \App\Models\Student::query()->count(),
            'buses_count' => class_exists('App\Models\Bus') ? \App\Models\Bus::query()->count() : 0,
            'buses_active' => class_exists('App\Models\Bus') ? \App\Models\Bus::query()->where('active', true)->count() : 0,
            'trips_today' => Trip::query()->whereDate('trip_date', $today)->count(),
            'trips_active' => Trip::query()->where('status', 'active')->count(),
            'reports_open' => Report::query()->where('status', 'open')->count(),
            'applications_pending' => \App\Models\Application::query()->where('status', 'pending')->count(),
            'service_requests_pending' => ServiceRequest::query()->where('status', 'pending')->count(),
        ];

        $recentApplications = \App\Models\Application::query()->latest()->limit(5)->get();

        $initialParents = \App\Models\ParentProfile::query()
            ->with(['user', 'students'])
            ->latest()
            ->get()
            ->map(fn (\App\Models\ParentProfile $parent) => [
                'id' => $parent->id,
                'name' => $parent->user?->name ?? 'Parent',
                'children' => $parent->students
                    ->map(fn ($student) => $student->full_name ?? $student->name)
                    ->filter()
                    ->join(', ') ?: 'None',
                'phone' => $parent->phone ?? '',
                'email' => $parent->user?->email ?? '',
                'applicationDate' => optional($parent->created_at)->toDateString(),
                'joinDate' => optional($parent->created_at)->toDateString(),
                'status' => $parent->active ? 'active' : 'inactive',
            ]);

        $initialDrivers = \App\Models\Driver::query()
            ->with('user')
            ->latest()
            ->get()
            ->map(fn (\App\Models\Driver $driver) => [
                'id' => $driver->id,
                'name' => $driver->user?->name ?? $driver->full_name ?? 'Driver',
                'license' => $driver->license_number ?? '',
                'phone' => $driver->phone ?? '',
                'applicationDate' => optional($driver->created_at)->toDateString(),
                'joinDate' => optional($driver->created_at)->toDateString(),
                'experience' => ((int) ($driver->years_experience ?? 0)) . ' years',
                'bus' => 'Assigned by trips',
                'status' => match ($driver->status) {
                    'approved' => 'active',
                    'rejected' => 'inactive',
                    default => $driver->status ?: ($driver->active ? 'active' : 'inactive'),
                },
            ]);

        return view('admin.admin', compact('stats', 'recentApplications', 'initialAdminPage', 'initialParents', 'initialDrivers'));
    }

    public function section(string $section)
    {
        $allowed = [
            'applications',
            'student-assignments',
            'school-profiles',
            'parents',
            'drivers',
            'buses',
            'reports',
            'requests',
            'school-requests',
            'account-recovery',
            'financials',
        ];

        abort_unless(in_array($section, $allowed, true), 404);

        return $this->dashboard($section);
    }

    public function dashboardData(Request $request)
    {
        $today = CarbonImmutable::today();

        return response()->json([
            'total_users' => User::query()->count(),
            'active_trips' => Trip::query()->where('status', 'active')->count(),
            'today_trips' => Trip::query()->whereDate('trip_date', $today)->count(),
            'open_reports' => Report::query()->where('status', 'open')->count(),
        ]);
    }
}
