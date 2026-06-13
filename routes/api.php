<?php

use App\Http\Controllers\Api\AdminActivityLogController;
use App\Http\Controllers\Api\AdminAttendanceController;
use App\Http\Controllers\Api\AdminStudentAssignmentController;
use App\Http\Controllers\Api\AdminSystemHealthController;
use App\Http\Controllers\Api\AdminApplicationController;
use App\Http\Controllers\Api\AdminBusController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminDriverController;
use App\Http\Controllers\Api\AdminNotificationController;
use App\Http\Controllers\Api\AdminParentController;
use App\Http\Controllers\Api\AdminRouteController;
use App\Http\Controllers\Api\AdminStudentController;
use App\Http\Controllers\Api\AdminTripController;
use App\Http\Controllers\Api\AdminTrackingController;
use App\Http\Controllers\Api\AdminMiscController;
use App\Http\Controllers\Api\AdminReportController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\PublicInquiryController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\DriverApiController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\RequestApiController;
use App\Http\Controllers\Api\SchoolAdminAttendanceController;
use App\Http\Controllers\Api\SchoolAdminBusController;
use App\Http\Controllers\Api\SchoolAdminDashboardController;
use App\Http\Controllers\Api\SchoolAdminDriverController;
use App\Http\Controllers\Api\SchoolAdminEmergencyController;
use App\Http\Controllers\Api\SchoolAdminNotificationController;
use App\Http\Controllers\Api\SchoolAdminParentController;
use App\Http\Controllers\Api\SchoolAdminReportController;
use App\Http\Controllers\Api\SchoolAdminRouteController;
use App\Http\Controllers\Api\SchoolAdminSettingsController;
use App\Http\Controllers\Api\SchoolAdminStudentController;
use App\Http\Controllers\Api\SchoolAdminTrackingController;
use App\Http\Controllers\Api\SchoolAdminTripController;
use App\Http\Controllers\Api\ServiceRequestController;
use Illuminate\Support\Facades\Route;

// ── Public Auth ──
Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
});

Route::post('/applications', [ApplicationController::class, 'store'])
    ->middleware(['application.token', 'throttle:30,1']);

// Public quote request (no auth required)
Route::post('/public/quote', [ServiceRequestController::class, 'storePublic'])
    ->middleware('throttle:30,1');

Route::post('/public/contact', [PublicInquiryController::class, 'contact'])
    ->middleware('throttle:30,1');

Route::post('/public/newsletter', [PublicInquiryController::class, 'newsletter'])
    ->middleware('throttle:30,1');

/*
|--------------------------------------------------------------------------
| ⚠️  TEMPORARY DEV-ONLY ROUTE — REMOVE BEFORE PRODUCTION ⚠️
|--------------------------------------------------------------------------
| GET /api/auth/login — allows browser-based login testing.
| Returns a Sanctum token for admin@safestep.com without a POST form.
| THIS IS INSECURE AND MUST NOT EXIST IN PRODUCTION.
|--------------------------------------------------------------------------
*/
/*
Route::get('/auth/login', function () {
    $request = new \Illuminate\Http\Request([
        'email'    => 'admin@safestep.com',
        'password' => 'password',
    ]);

    return app(\App\Http\Controllers\Api\ApiAuthController::class)->login($request);
});
*/

// ── Authenticated ──
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/profile', [ApiAuthController::class, 'profile']);
        Route::put('/profile', [ApiAuthController::class, 'updateProfile']);
        Route::post('/change-password', [ApiAuthController::class, 'changePassword']);
    });

    Route::post('/requests', [RequestApiController::class, 'store']);

    // Service Requests (dedicated parent/driver request system)
    Route::post('/service-requests', [ServiceRequestController::class, 'store']);
    Route::get('/service-requests/my', [ServiceRequestController::class, 'myRequests']);

    // Unified applications endpoint (user-specific)
    Route::get('/applications', [ApplicationController::class, 'index']);

    // ── Admin routes ──
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);
        Route::get('/dashboard/attendance-summary', [AdminDashboardController::class, 'attendanceSummary']);
        Route::get('/dashboard/trips-overview', [AdminDashboardController::class, 'tripsOverview']);
        Route::get('/dashboard/fleet-status', [AdminDashboardController::class, 'fleetStatus']);
        Route::get('/system/health', [AdminSystemHealthController::class, 'health']);
        Route::get('/system/school-performance', [AdminSystemHealthController::class, 'schoolPerformance']);
        Route::get('/system/emergency-overview', [AdminSystemHealthController::class, 'emergencyOverview']);
        Route::get('/activity-logs', [AdminActivityLogController::class, 'index']);

        // Live bus tracking (Alexandria)
        Route::get('/tracking/live', [AdminTrackingController::class, 'live']);
        Route::get('/tracking/buses/{bus}', [AdminTrackingController::class, 'bus']);
        Route::get('/tracking/trips/{trip}/history', [AdminTrackingController::class, 'tripHistory']);

        // Students CRUD
        Route::get('/students', [AdminStudentController::class, 'index']);
        Route::get('/students/{student}', [AdminStudentController::class, 'show']);
        Route::post('/students', [AdminStudentController::class, 'store']);
        Route::put('/students/{student}', [AdminStudentController::class, 'update']);
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy']);
        Route::get('/student-assignments', [AdminStudentAssignmentController::class, 'index']);
        Route::post('/student-assignments/assign', [AdminStudentAssignmentController::class, 'assign']);

        // Drivers CRUD
        Route::get('/drivers', [AdminDriverController::class, 'index']);
        Route::get('/drivers/pending', [AdminDriverController::class, 'pending']);
        Route::get('/drivers/{driver}', [AdminDriverController::class, 'show']);
        Route::post('/drivers', [AdminDriverController::class, 'store']);
        Route::put('/drivers/{driver}', [AdminDriverController::class, 'update']);
        Route::delete('/drivers/{driver}', [AdminDriverController::class, 'destroy']);
        Route::post('/drivers/{driver}/approve', [AdminDriverController::class, 'approve']);
        Route::post('/drivers/{driver}/reject', [AdminDriverController::class, 'reject']);

        // Parents CRUD
        Route::get('/parents', [AdminParentController::class, 'index']);
        Route::get('/parents/{parent}', [AdminParentController::class, 'show']);
        Route::post('/parents', [AdminParentController::class, 'store']);
        Route::put('/parents/{parent}', [AdminParentController::class, 'update']);
        Route::delete('/parents/{parent}', [AdminParentController::class, 'destroy']);
        Route::post('/parents/{parent}/approve', [AdminParentController::class, 'approve']);
        Route::post('/parents/{parent}/reject', [AdminParentController::class, 'reject']);

        // Buses CRUD
        Route::get('/buses', [AdminBusController::class, 'index']);
        Route::get('/buses/capacity', [AdminBusController::class, 'capacity']);
        Route::get('/buses/{bus}', [AdminBusController::class, 'show']);
        Route::post('/buses', [AdminBusController::class, 'store']);
        Route::put('/buses/{bus}', [AdminBusController::class, 'update']);
        Route::delete('/buses/{bus}', [AdminBusController::class, 'destroy']);

        // Routes CRUD
        Route::get('/routes', [AdminRouteController::class, 'index']);
        Route::get('/routes/{route}', [AdminRouteController::class, 'show']);
        Route::post('/routes', [AdminRouteController::class, 'store']);
        Route::put('/routes/{route}', [AdminRouteController::class, 'update']);
        Route::delete('/routes/{route}', [AdminRouteController::class, 'destroy']);
        Route::post('/routes/{route}/stops', [AdminRouteController::class, 'addStop']);

        // Trips CRUD
        Route::get('/trips', [AdminTripController::class, 'index']);
        Route::get('/trips/{trip}', [AdminTripController::class, 'show']);
        Route::post('/trips', [AdminTripController::class, 'store']);
        Route::put('/trips/{trip}', [AdminTripController::class, 'update']);
        Route::delete('/trips/{trip}', [AdminTripController::class, 'destroy']);
        Route::post('/trips/{trip}/start', [AdminTripController::class, 'start']);
        Route::post('/trips/{trip}/complete', [AdminTripController::class, 'complete']);
        Route::post('/trips/{trip}/cancel', [AdminTripController::class, 'cancel']);

        // Attendance
        Route::get('/attendance', [AdminAttendanceController::class, 'index']);
        Route::get('/attendance/{attendance}', [AdminAttendanceController::class, 'show']);
        Route::post('/attendance', [AdminAttendanceController::class, 'store']);
        Route::put('/attendance/{attendance}', [AdminAttendanceController::class, 'update']);
        Route::delete('/attendance/{attendance}', [AdminAttendanceController::class, 'destroy']);

        // Notifications
        Route::get('/notifications', [AdminNotificationController::class, 'index']);
        Route::post('/notifications/send', [AdminNotificationController::class, 'send']);
        Route::post('/notifications/send-bulk', [AdminNotificationController::class, 'sendBulk']);

        // Users
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
        Route::get('/roles', [AdminUserController::class, 'roles']);

        // Misc (Schools, Financials, Maintenance)
        Route::get('/schools', [AdminMiscController::class, 'indexSchools']);
        Route::post('/schools', [AdminMiscController::class, 'storeSchool']);
        Route::post('/schools/{school}/approve', [AdminMiscController::class, 'approveSchool']);
        Route::post('/schools/{school}/reject', [AdminMiscController::class, 'rejectSchool']);
        Route::get('/financial-entries', [AdminMiscController::class, 'indexFinancialEntries']);
        Route::post('/financial-entries', [AdminMiscController::class, 'storeFinancialEntry']);
        Route::get('/maintenance-records', [AdminMiscController::class, 'indexMaintenanceRecords']);
        Route::post('/maintenance-records', [AdminMiscController::class, 'storeMaintenanceRecord']);

        // Reports / complaints
        Route::get('/reports', [AdminReportController::class, 'index']);
        Route::post('/reports', [AdminReportController::class, 'store']);
        Route::patch('/reports/{report}', [AdminReportController::class, 'update']);

        // Applications
        Route::get('/applications', [AdminApplicationController::class, 'index']);
        Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus']);

        // Service Requests
        Route::get('/service-requests', [ServiceRequestController::class, 'index']);
        Route::get('/service-requests/stats', [ServiceRequestController::class, 'stats']);
        Route::get('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'show']);
        Route::put('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'update']);
    });

    // ── Driver routes ──
    Route::prefix('driver')->middleware('role:driver')->group(function () {
        Route::post('/details/submit', [DriverApiController::class, 'submitDetails']);
        Route::get('/dashboard', [DriverApiController::class, 'dashboard']);
        Route::get('/trips/today', [DriverApiController::class, 'todayTrips']);
        Route::post('/trips/{trip}/start', [DriverApiController::class, 'startTrip']);
        Route::post('/trips/{trip}/complete', [DriverApiController::class, 'completeTrip']);
        Route::get('/trips/{trip}/students', [DriverApiController::class, 'tripStudents']);
        Route::post('/trips/{trip}/attendance', [DriverApiController::class, 'markAttendance']);
        Route::post('/location', [DriverApiController::class, 'updateLocation']);
        Route::get('/students', [DriverApiController::class, 'myStudents']);
        Route::post('/trips/report-issue', [DriverApiController::class, 'reportTripIssue']);
        Route::get('/notifications', [DriverApiController::class, 'notifications']);
        Route::get('/requests', [DriverApiController::class, 'requests']);
        Route::get('/route-progress', [DriverApiController::class, 'routeProgress']);
        Route::get('/performance', [DriverApiController::class, 'performance']);
        Route::get('/maintenance-alerts', [DriverApiController::class, 'maintenanceAlerts']);
    });

    // ── School Admin routes ──
    Route::prefix('school-admin')->middleware('role:school_admin')->group(function () {
        Route::post('/details/submit', [SchoolAdminDashboardController::class, 'submitDetails']);
        Route::get('/dashboard/stats', [SchoolAdminDashboardController::class, 'stats']);
        Route::get('/dashboard/attendance-summary', [SchoolAdminDashboardController::class, 'attendanceSummary']);
        Route::get('/dashboard/trips-overview', [SchoolAdminDashboardController::class, 'tripsOverview']);
        Route::get('/dashboard/fleet-status', [SchoolAdminDashboardController::class, 'fleetStatus']);
        Route::get('/dashboard/attendance-trends', [SchoolAdminDashboardController::class, 'attendanceTrends']);
        Route::get('/dashboard/safety-reports', [SchoolAdminDashboardController::class, 'safetyReports']);
        Route::get('/dashboard/kpis', [SchoolAdminDashboardController::class, 'kpis']);
        Route::get('/dashboard/student-risk', [SchoolAdminDashboardController::class, 'studentRisk']);

        Route::get('/parents', [SchoolAdminParentController::class, 'index']);
        Route::get('/parents/{parent}', [SchoolAdminParentController::class, 'show']);

        Route::get('/students', [SchoolAdminStudentController::class, 'index']);
        Route::get('/students/{student}', [SchoolAdminStudentController::class, 'show']);
        Route::post('/students', [SchoolAdminStudentController::class, 'store']);
        Route::put('/students/{student}', [SchoolAdminStudentController::class, 'update']);
        Route::delete('/students/{student}', [SchoolAdminStudentController::class, 'destroy']);
        Route::get('/students/{student}/qr', [SchoolAdminStudentController::class, 'qrCode']);

        Route::get('/buses', [SchoolAdminBusController::class, 'index']);
        Route::get('/buses/{bus}', [SchoolAdminBusController::class, 'show']);
        Route::post('/buses', [SchoolAdminBusController::class, 'store']);
        Route::put('/buses/{bus}', [SchoolAdminBusController::class, 'update']);
        Route::delete('/buses/{bus}', [SchoolAdminBusController::class, 'destroy']);
        Route::get('/maintenance-records', [SchoolAdminBusController::class, 'maintenance']);
        Route::post('/maintenance-records', [SchoolAdminBusController::class, 'storeMaintenance']);

        Route::get('/drivers', [SchoolAdminDriverController::class, 'index']);
        Route::get('/drivers/{driver}', [SchoolAdminDriverController::class, 'show']);
        Route::put('/drivers/{driver}', [SchoolAdminDriverController::class, 'update']);

        Route::get('/routes', [SchoolAdminRouteController::class, 'index']);
        Route::get('/routes/{route}', [SchoolAdminRouteController::class, 'show']);
        Route::post('/routes', [SchoolAdminRouteController::class, 'store']);
        Route::put('/routes/{route}', [SchoolAdminRouteController::class, 'update']);
        Route::delete('/routes/{route}', [SchoolAdminRouteController::class, 'destroy']);
        Route::post('/routes/{route}/stops', [SchoolAdminRouteController::class, 'addStop']);

        Route::get('/trips', [SchoolAdminTripController::class, 'index']);
        Route::get('/trips/{trip}', [SchoolAdminTripController::class, 'show']);
        Route::post('/trips', [SchoolAdminTripController::class, 'store']);
        Route::put('/trips/{trip}', [SchoolAdminTripController::class, 'update']);
        Route::delete('/trips/{trip}', [SchoolAdminTripController::class, 'destroy']);

        Route::get('/attendance', [SchoolAdminAttendanceController::class, 'index']);
        Route::get('/attendance/reports', [SchoolAdminAttendanceController::class, 'reports']);

        Route::get('/notifications', [SchoolAdminNotificationController::class, 'index']);
        Route::get('/notifications/center', [SchoolAdminNotificationController::class, 'center']);
        Route::post('/notifications/send', [SchoolAdminNotificationController::class, 'send']);
        Route::post('/notifications/send-bulk', [SchoolAdminNotificationController::class, 'sendBulk']);

        Route::get('/tracking/live', [SchoolAdminTrackingController::class, 'live']);
        Route::get('/tracking/buses/{bus}', [SchoolAdminTrackingController::class, 'bus']);
        Route::get('/tracking/trips/{trip}/history', [SchoolAdminTrackingController::class, 'tripHistory']);

        Route::get('/emergency-alerts', [SchoolAdminEmergencyController::class, 'index']);
        Route::post('/emergency-alerts', [SchoolAdminEmergencyController::class, 'store']);
        Route::post('/emergency-alerts/{emergencyAlert}/resolve', [SchoolAdminEmergencyController::class, 'resolve']);

        Route::get('/reports', [SchoolAdminReportController::class, 'index']);
        Route::get('/reports/export', [SchoolAdminReportController::class, 'export']);

        Route::get('/settings', [SchoolAdminSettingsController::class, 'show']);
        Route::put('/settings/school', [SchoolAdminSettingsController::class, 'updateSchool']);
        Route::put('/settings/profile', [SchoolAdminSettingsController::class, 'updateProfile']);
        Route::get('/activity-logs', [SchoolAdminSettingsController::class, 'activityLogs']);
    });

    // ── Parent routes ──
    Route::prefix('parent')->middleware('role:parent')->group(function () {
        Route::get('/dashboard', [ParentApiController::class, 'dashboard']);
        Route::get('/children', [ParentApiController::class, 'children']);
        Route::post('/children/submit', [ParentApiController::class, 'submitChildren']);
        Route::get('/children/{student}', [ParentApiController::class, 'child']);
        Route::get('/children/{student}/attendance', [ParentApiController::class, 'childAttendance']);
        Route::get('/notifications', [ParentApiController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [ParentApiController::class, 'markNotificationRead']);
        Route::post('/notifications/read-all', [ParentApiController::class, 'markAllRead']);
        Route::get('/requests', [ParentApiController::class, 'requests']);
        Route::get('/children/{student}/trip-history', [ParentApiController::class, 'childTripHistory']);
        Route::get('/attendance-report', [ParentApiController::class, 'attendanceReport']);
        Route::get('/emergency-status', [ParentApiController::class, 'emergencyStatus']);
    });
});
