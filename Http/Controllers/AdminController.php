<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Trip;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = CarbonImmutable::today();

        $stats = [
            'users_total' => User::query()->count(),
            'trips_today' => Trip::query()->whereDate('trip_date', $today)->count(),
            'trips_active' => Trip::query()->where('status', 'active')->count(),
            'reports_open' => Report::query()->where('status', 'open')->count(),
        ];

        return view('admin.admin', compact('stats'));
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

