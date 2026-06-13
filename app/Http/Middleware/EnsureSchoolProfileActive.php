<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolProfileActive
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $school = $user?->school;

        if (! $school) {
            return response()->json([
                'success' => false,
                'message' => 'School administrator is not linked to a school.',
            ], 403);
        }

        if ($school->status === 'active') {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => $school->status === 'pending_details'
                ? 'Complete and submit your school profile to unlock this section.'
                : 'Your school profile is under admin review. This section will unlock after approval.',
            'school_status' => $school->status,
        ], 403);
    }
}
