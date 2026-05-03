<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    /**
     * Display parent applications dashboard.
     */
    public function index()
    {
        $applications = Application::where('role', 'parent')
            ->latest()
            ->paginate(10);

        return view('dashboard.parent-applications', compact('applications'));
    }

    /**
     * Return application details as JSON (for modal).
     */
    public function show(Application $application)
    {
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
