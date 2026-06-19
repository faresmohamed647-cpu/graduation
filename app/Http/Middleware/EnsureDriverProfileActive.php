<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDriverProfileActive
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $driver = $user?->driverProfile;

        if (! $driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found.',
            ], 403);
        }

        if ($driver->status === 'approved') {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => $driver->status === 'pending_details'
                ? 'Complete and submit your driver profile to unlock this section.'
                : 'Your driver profile is under admin review. This section will unlock after approval.',
            'driver_status' => $driver->status,
        ], 403);
    }
}
