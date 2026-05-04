<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class DriverDashboardController extends Controller
{
    /**
     * Display driver applications dashboard.
     * Shows ONLY the authenticated user's applications.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $applications = Application::where(function ($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('email', auth()->user()->email);
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.driver-applications', compact('applications'));
    }

    /**
     * Return application details as JSON (for modal).
     */
    public function show(Request $request, Application $application)
    {
        $user = $request->user();

        // Ensure user can only view their own applications
        if ($application->user_id !== $user->id && $application->email !== $user->email) {
            abort(403, 'Unauthorized');
        }

        $metadata = [];
        $cleanNotes = $application->notes;

        if ($application->notes && str_contains($application->notes, 'meta:')) {
            $parts = explode('meta:', $application->notes, 2);
            $cleanNotes = trim($parts[0]);
            $metadata = json_decode($parts[1] ?? '{}', true) ?: [];
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'         => $application->id,
                'full_name'  => $application->full_name,
                'email'      => $application->email,
                'phone'      => $application->phone,
                'address'    => $application->address,
                'role'       => $application->role,
                'experience' => $application->experience,
                'notes'      => $cleanNotes,
                'metadata'   => $metadata,
                'status'     => $application->status,
                'created_at' => $application->created_at->format('M d, Y h:i A'),
            ],
        ]);
    }
}
