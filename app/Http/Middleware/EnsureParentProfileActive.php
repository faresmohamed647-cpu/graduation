<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParentProfileActive
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $parent = $user?->parentProfile;

        if (! $parent) {
            return response()->json([
                'success' => false,
                'message' => 'Parent profile not found.',
            ], 403);
        }

        $status = strtolower((string) $parent->status);

        if ($status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Your profile was rejected. Please contact support or resubmit your details.',
                'parent_status' => $parent->status,
            ], 403);
        }

        if ($parent->profile_approved_at || $status === 'approved') {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => $parent->status === 'pending_details'
                ? 'Complete and submit your children details form to unlock this section.'
                : 'Your children details are under admin review. This section will unlock after approval.',
            'parent_status' => $parent->status,
        ], 403);
    }
}
