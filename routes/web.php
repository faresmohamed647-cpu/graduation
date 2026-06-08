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
use App\Http\Controllers\SchoolAdminController;
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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware(['throttle:10,1']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Dedicated dashboard paths (used by role-based login redirect)
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::redirect('/admin', '/admin')->middleware('role:admin');
    Route::redirect('/school-admin', '/school-admin')->middleware('role:school_admin');

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
Route::view('/join', 'website.join');
Route::view('/apply/parent', 'website.apply-parent');
Route::view('/apply/driver', 'website.apply-driver');
Route::view('/apply/school', 'website.apply-school');
Route::view('/apply/admin',  'website.apply-admin');
Route::redirect('/apply', '/join');
Route::view('/parents',      'website.parent-portal'); // New Premium Portal

// Static website pages (Blade views already exist under resources/views/website)
$websitePages = [
    'about',
    'contact',
    'faq',
    'feature',
    'how-it-works',
    'pay',
    'parent-portal',
    'price',
    'quote',
    'service',
    'tracking',
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
    Route::get('/', [AdminController::class, 'dashboard']);

    // Original admin dashboard sections
    Route::get('/applications', [AdminController::class, 'section'])->defaults('section', 'applications');
    Route::get('/parents', [AdminController::class, 'section'])->defaults('section', 'parents');
    Route::get('/drivers', [AdminController::class, 'section'])->defaults('section', 'drivers');
    Route::get('/buses', [AdminController::class, 'section'])->defaults('section', 'buses');
    Route::get('/reports', [AdminController::class, 'section'])->defaults('section', 'reports');
    Route::get('/requests', [AdminController::class, 'section'])->defaults('section', 'requests');
    Route::get('/account-recovery', [AdminController::class, 'section'])->defaults('section', 'account-recovery');
    Route::get('/financials', [AdminController::class, 'section'])->defaults('section', 'financials');

    // Applications web actions kept for legacy forms/modals
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show']);
    Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus']);
    Route::delete('/applications/{application}', [AdminApplicationController::class, 'destroy']);

    // Admin sub-pages that already exist as Blade (keep UI, prevent 404)
    // All legacy admin pages are now redirected to the unified SPA dashboard
    Route::redirect('/add-user', '/admin');
    Route::redirect('/add-trip', '/admin');
    Route::redirect('/add-student', '/admin');
    Route::redirect('/add-school', '/admin');
    Route::redirect('/add-parent', '/admin');
    Route::redirect('/add-driver', '/admin');
    Route::redirect('/add-bus', '/admin');
    Route::redirect('/add-complaint', '/admin');
    Route::redirect('/add-maintenance-record', '/admin');
    Route::redirect('/add-financial-entry', '/admin');
    Route::redirect('/add-entry', '/admin');
    Route::redirect('/price', '/admin');
    Route::redirect('/qr', '/admin');

    // Legacy .html links used inside templates
    Route::redirect('/admin.html', '/admin', 301);
});

Route::prefix('driver')->middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/', [DriverController::class, 'dashboard']);
    Route::get('/report', [DriverController::class, 'report']);
    Route::get('/request', [DriverController::class, 'requests']);
    Route::get('/applications', [DriverController::class, 'applications']);

    Route::post('/trips/{trip}/start', [TripController::class, 'start']);
    Route::post('/trips/{trip}/end', [TripController::class, 'end']);
    Route::post('/trips/{trip}/pickup', [TripController::class, 'pickup']);
    Route::post('/trips/{trip}/dropoff', [TripController::class, 'dropoff']);

    Route::redirect('/driver.html', '/driver', 301);
    Route::redirect('/driver-request.html', '/driver/request', 301);
});

Route::prefix('school-admin')->middleware(['auth', 'role:school_admin'])->group(function () {
    Route::get('/', [SchoolAdminController::class, 'dashboard']);
    Route::get('/parents', [SchoolAdminController::class, 'section'])->defaults('section', 'parents');
    Route::get('/students', [SchoolAdminController::class, 'section'])->defaults('section', 'students');
    Route::get('/buses', [SchoolAdminController::class, 'section'])->defaults('section', 'buses');
    Route::get('/drivers', [SchoolAdminController::class, 'section'])->defaults('section', 'drivers');
    Route::get('/routes', [SchoolAdminController::class, 'section'])->defaults('section', 'routes');
    Route::get('/trips', [SchoolAdminController::class, 'section'])->defaults('section', 'trips');
    Route::get('/attendance', [SchoolAdminController::class, 'section'])->defaults('section', 'attendance');
    Route::get('/notifications', [SchoolAdminController::class, 'section'])->defaults('section', 'notifications');
    Route::get('/emergency', [SchoolAdminController::class, 'section'])->defaults('section', 'emergency');
    Route::get('/reports', [SchoolAdminController::class, 'section'])->defaults('section', 'reports');
    Route::get('/tracking', [SchoolAdminController::class, 'section'])->defaults('section', 'tracking');
    Route::get('/settings', [SchoolAdminController::class, 'section'])->defaults('section', 'settings');
    Route::get('/activity-logs', [SchoolAdminController::class, 'section'])->defaults('section', 'activity-logs');
    Route::redirect('/school-admin.html', '/school-admin', 301);
});

Route::prefix('parent')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/', [ParentController::class, 'dashboard']);
    Route::get('/report', [ParentController::class, 'report']);
    Route::get('/request', [ParentController::class, 'requests']);
    Route::get('/applications', [ParentController::class, 'applications']);

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
