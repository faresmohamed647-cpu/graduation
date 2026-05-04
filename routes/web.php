<?php

use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('website.index');
});

Route::view('/apply', 'website.apply');

// Legacy static entrypoints (avoid 404s from migrated HTML)
Route::redirect('/index.html', '/', 301);
Route::view('/404', 'website.404');
Route::redirect('/404.html', '/404', 301);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware(['throttle:10,1']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Dedicated dashboard paths (used by role-based login redirect)
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::redirect('/admin', '/admin/applications')->middleware('role:admin');

    Route::get('/driver', [DriverDashboardController::class, 'index'])->middleware('role:driver');
    Route::get('/driver/applications/{application}', [DriverDashboardController::class, 'show'])->middleware('role:driver');

    Route::get('/parent', [ParentDashboardController::class, 'index'])->middleware('role:parent');
    Route::get('/parent/applications/{application}', [ParentDashboardController::class, 'show'])->middleware('role:parent');
});

// Registration & Applications
Route::post('/register/parent', [RegistrationController::class, 'registerParent']);
Route::post('/register/driver', [RegistrationController::class, 'registerDriver']);
Route::post('/apply/submit', [ApplicationController::class, 'submit']);

// Public Application Pages
Route::view('/apply/parent', 'website.apply-parent');
Route::view('/apply/driver', 'website.apply-driver');
Route::view('/apply/admin',  'website.apply-admin');
Route::view('/parents',      'website.parent-portal'); // New Premium Portal

// Static website pages (Blade views already exist under resources/views/website)
$websitePages = [
    'about',
    'contact',
    'feature',
    'pay',
    'parent-portal',
    'price',
    'quote',
    'service',
    'team',
    'testimonial',
];

Route::get('/verify', function () {
    return view('website.verify');
});
Route::redirect('/verify.html', '/verify', 301);

foreach ($websitePages as $page) {
    Route::view('/'.$page, 'website.'.$page);
    Route::redirect('/'.$page.'.html', '/'.$page, 301);
}

// Protected role dashboards (static views for now)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::redirect('/', '/admin/applications');

    // Applications management
    Route::get('/applications', [AdminApplicationController::class, 'index']);
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show']);
    Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus']);
    Route::delete('/applications/{application}', [AdminApplicationController::class, 'destroy']);

    // Admin sub-pages that already exist as Blade (keep UI, prevent 404)
    // All legacy admin pages are now redirected to the unified SPA dashboard
    Route::redirect('/add-user', '/admin/applications');
    Route::redirect('/add-trip', '/admin/applications');
    Route::redirect('/add-student', '/admin/applications');
    Route::redirect('/add-school', '/admin/applications');
    Route::redirect('/add-parent', '/admin/applications');
    Route::redirect('/add-driver', '/admin/applications');
    Route::redirect('/add-bus', '/admin/applications');
    Route::redirect('/add-complaint', '/admin/applications');
    Route::redirect('/add-maintenance-record', '/admin/applications');
    Route::redirect('/add-financial-entry', '/admin/applications');
    Route::redirect('/add-entry', '/admin/applications');
    Route::redirect('/price', '/admin/applications');
    Route::redirect('/qr', '/admin/applications');

    // Legacy .html links used inside templates
    Route::redirect('/admin.html', '/admin', 301);
});

Route::prefix('driver')->middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/', [DriverController::class, 'dashboard']);
    Route::get('/report', [DriverController::class, 'report']);
    Route::get('/request', function () {
        return view('driver.driver-request', ['apiToken' => session('api_token', '')]);
    });

    Route::post('/trips/{trip}/start', [TripController::class, 'start']);
    Route::post('/trips/{trip}/end', [TripController::class, 'end']);
    Route::post('/trips/{trip}/pickup', [TripController::class, 'pickup']);
    Route::post('/trips/{trip}/dropoff', [TripController::class, 'dropoff']);

    Route::redirect('/driver.html', '/driver', 301);
    Route::redirect('/driver-request.html', '/driver/request', 301);
});

Route::prefix('parent')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/', [ParentController::class, 'dashboard']);
    Route::get('/report', [ParentController::class, 'report']);
    Route::get('/request', function () {
        return view('parent.parent-request', ['apiToken' => session('api_token', '')]);
    });

    Route::redirect('/parent.html', '/parent', 301);
    Route::redirect('/parent-request.html', '/parent/request', 301);
});

// Additional legacy paths referenced by older landing pages
Route::redirect('/Admin/admin.html', '/admin', 301);
Route::redirect('/Driver/driver.html', '/driver', 301);
Route::redirect('/Parents/parent.html', '/parent', 301);

Route::get('/reports', [ReportController::class, 'index'])->middleware('auth');
Route::post('/reports', [ReportController::class, 'store'])->middleware('auth');

Route::prefix('notifications')->middleware('auth')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/', [NotificationController::class, 'store'])->middleware('role:admin');
    Route::post('/{id}/read', [NotificationController::class, 'markRead']);
});
