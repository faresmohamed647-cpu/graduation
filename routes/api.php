<?php

use App\Http\Controllers\Api\AdminAttendanceController;
use App\Http\Controllers\Api\AdminBusController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminDriverController;
use App\Http\Controllers\Api\AdminNotificationController;
use App\Http\Controllers\Api\AdminParentController;
use App\Http\Controllers\Api\AdminRouteController;
use App\Http\Controllers\Api\AdminStudentController;
use App\Http\Controllers\Api\AdminTripController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\DriverApiController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\RequestApiController;
use Illuminate\Support\Facades\Route;

// ── Public Auth ──
Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
});

Route::post('/applications', [ApplicationController::class, 'store'])
    ->middleware(['application.token', 'throttle:30,1']);

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

    // ── Admin routes ──
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);
        Route::get('/dashboard/attendance-summary', [AdminDashboardController::class, 'attendanceSummary']);
        Route::get('/dashboard/trips-overview', [AdminDashboardController::class, 'tripsOverview']);
        Route::get('/dashboard/fleet-status', [AdminDashboardController::class, 'fleetStatus']);

        // Students CRUD
        Route::get('/students', [AdminStudentController::class, 'index']);
        Route::get('/students/{student}', [AdminStudentController::class, 'show']);
        Route::post('/students', [AdminStudentController::class, 'store']);
        Route::put('/students/{student}', [AdminStudentController::class, 'update']);
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy']);

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
    });

    // ── Driver routes ──
    Route::prefix('driver')->middleware('role:driver')->group(function () {
        Route::get('/dashboard', [DriverApiController::class, 'dashboard']);
        Route::get('/trips/today', [DriverApiController::class, 'todayTrips']);
        Route::post('/trips/{trip}/start', [DriverApiController::class, 'startTrip']);
        Route::post('/trips/{trip}/complete', [DriverApiController::class, 'completeTrip']);
        Route::get('/trips/{trip}/students', [DriverApiController::class, 'tripStudents']);
        Route::post('/trips/{trip}/attendance', [DriverApiController::class, 'markAttendance']);
        Route::post('/location', [DriverApiController::class, 'updateLocation']);
        Route::get('/students', [DriverApiController::class, 'myStudents']);
        Route::get('/notifications', [DriverApiController::class, 'notifications']);
    });

    // ── Parent routes ──
    Route::prefix('parent')->middleware('role:parent')->group(function () {
        Route::get('/dashboard', [ParentApiController::class, 'dashboard']);
        Route::get('/children', [ParentApiController::class, 'children']);
        Route::get('/children/{student}', [ParentApiController::class, 'child']);
        Route::get('/children/{student}/attendance', [ParentApiController::class, 'childAttendance']);
        Route::get('/notifications', [ParentApiController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [ParentApiController::class, 'markNotificationRead']);
        Route::post('/notifications/read-all', [ParentApiController::class, 'markAllRead']);
    });
});
