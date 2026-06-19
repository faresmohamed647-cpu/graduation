<?php

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureApplicationApiToken;
use App\Http\Middleware\EnsureSchoolProfileActive;
use App\Http\Middleware\ForceApiJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            ForceApiJsonResponse::class,
        ]);

        $middleware->alias([
            'role' => EnsureRole::class,
            'application.token' => EnsureApplicationApiToken::class,
            'school.active' => EnsureSchoolProfileActive::class,
            'driver.active' => \App\Http\Middleware\EnsureDriverProfileActive::class,
            'parent.active' => \App\Http\Middleware\EnsureParentProfileActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e): bool {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated',
                    'errors' => ['authorization' => ['Authentication is required.']],
                ], 401);
            }

            return null;
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                $errors = $e->errors();
                $firstMessage = collect($errors)->flatten()->filter()->first();

                return response()->json([
                    'status' => 'error',
                    'message' => $firstMessage ?: 'Validation failed',
                    'errors' => $errors,
                ], 422);
            }

            return null;
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Server error',
                    'errors' => ['server' => ['An unexpected server error occurred.']],
                ], 500);
            }

            return null;
        });
    })->create();
