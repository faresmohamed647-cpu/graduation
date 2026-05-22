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

        return view('admin.admin', compact('stats', 'recentApplications', 'initialAdminPage'));
    }

    public function section(string $section)
    {
        $allowed = [
            'applications',
            'parents',
            'drivers',
            'buses',
            'reports',
            'requests',
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
