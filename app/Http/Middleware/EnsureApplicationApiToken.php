<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('services.applications.token', '');
        $providedToken = (string) ($request->bearerToken() ?: $request->header('X-Application-Token', ''));

        if ($configuredToken === '' || ! hash_equals($configuredToken, $providedToken)) {
            Log::warning('Unauthorized applications API access attempt', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized request',
                'errors' => ['authorization' => ['Invalid or missing API token.']],
            ], 401);
        }

        return $next($request);
    }
}
