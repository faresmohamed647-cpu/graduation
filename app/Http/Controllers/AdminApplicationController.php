<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminApplicationController extends Controller
{
    /**
     * Display paginated list of applications with optional filtering.
     */
    public function index(Request $request)
    {
        $query = Application::query()->latest();

        // Filter by role
        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->where('role', $request->input('role'));
        }

        // Filter by status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Search by name or phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total'    => Application::count(),
            'pending'  => Application::where('status', 'pending')->count(),
            'accepted' => Application::where('status', 'accepted')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        return view('admin.applications', compact('applications', 'stats'));
    }

    /**
     * Return application details as JSON (for modal).
     */
    public function show(Application $application)
    {
        // Parse role-specific metadata from notes
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
                'updated_at' => $application->updated_at->format('M d, Y h:i A'),
            ],
        ]);
    }

    /**
     * Update application status (pending → reviewed → accepted / rejected).
     */
    public function updateStatus(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'accepted', 'rejected'])],
        ]);

        $application->update(['status' => $validated['status']]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Application status updated to ' . $validated['status'] . '.',
            'data'    => [
                'id'     => $application->id,
                'status' => $application->status,
            ],
        ]);
    }

    /**
     * Delete an application record.
     */
    public function destroy(Application $application)
    {
        $application->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Application deleted successfully.',
        ]);
    }
}
