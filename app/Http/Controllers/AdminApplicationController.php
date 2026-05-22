<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Driver;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $query->where(DB::raw('LOWER(role)'), strtolower($request->input('role')));
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

        // Ensure we always have a valid Sanctum token for the dashboard JS.
        // If the session lost it (e.g. server restart), create one on-the-fly.
        $apiToken = session('api_token');
        if (!$apiToken && auth()->check()) {
            $user = auth()->user();
            $user->tokens()->where('name', 'dashboard-session')->delete();
            $apiToken = $user->createToken('dashboard-session')->plainTextToken;
            session(['api_token' => $apiToken]);
        }

        return view('admin.applications', [
            'applications' => $applications,
            'stats'        => $stats,
            'apiToken'     => $apiToken ?? '',
            'adminName'    => auth()->user()?->name ?? 'Admin',
        ]);
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

        if (in_array($validated['status'], ['accepted', 'rejected'], true)) {
            $role = strtolower((string) $application->role);
            $active = $validated['status'] === 'accepted';
            $meta = $application->metadata; // parsed JSON from notes column

            $user = null;
            if ($application->user_id) {
                $user = User::find($application->user_id);
            }
            if (!$user) {
                $user = User::where('email', $application->email)->first();
            }

            if ($active && !$user) {
                $user = User::create([
                    'name' => $application->full_name,
                    'email' => $application->email,
                    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                    'plain_password' => 'password123',
                    'role' => $role,
                ]);
            }

            if ($user) {
                if ($active) {
                    $user->update(['role' => $role]);
                }
                if ($application->user_id !== $user->id) {
                    $application->update(['user_id' => $user->id]);
                }

                if ($role === 'parent') {
                    ParentProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        array_filter([
                            'active'           => $active,
                            'phone'            => $application->phone ?: ($meta['phone'] ?? null),
                            'address'          => $application->address ?: ($meta['address'] ?? null),
                            'state'            => $meta['student_state'] ?? ($meta['state'] ?? null),
                            'relationship'     => $meta['student_relationship'] ?? ($meta['relationship'] ?? null),
                            'student_count'    => $meta['student_count'] ?? null,
                            'degree'           => $meta['student_degree'] ?? ($meta['degree'] ?? null),
                            'education_system' => $meta['student_education_system'] ?? ($meta['education_system'] ?? null),
                            'school_name'      => $meta['school_name'] ?? null,
                            'school_address'   => $meta['school_address'] ?? null,
                            'school_starting'  => $meta['school_starting'] ?? null,
                            'message'          => $meta['message'] ?? $application->clean_notes,
                        ], fn ($v) => $v !== null)
                    );
                } elseif ($role === 'driver') {
                    Driver::updateOrCreate(
                        ['user_id' => $user->id],
                        array_filter([
                            'active'           => $active,
                            'status'           => $active ? 'approved' : 'rejected',
                            'full_name'        => $application->full_name ?: ($meta['full_name'] ?? null),
                            'phone'            => $application->phone ?: ($meta['phone'] ?? null),
                            'address'          => $application->address ?: ($meta['address'] ?? null),
                            'license_number'   => $meta['license_number'] ?? null,
                            'years_experience' => isset($meta['years_experience']) ? (int) $meta['years_experience'] : null,
                            'age'              => $meta['owner_age'] ?? ($meta['age'] ?? null),
                            'gender'           => $meta['owner_gender'] ?? ($meta['gender'] ?? null),
                            'car_type'         => $meta['car_type'] ?? null,
                            'car_model'        => $meta['car_model'] ?? null,
                            'car_plate'        => $meta['car_plate'] ?? null,
                            'state'            => $meta['owner_state'] ?? ($meta['state'] ?? null),
                            'message'          => $meta['message'] ?? $application->clean_notes,
                        ], fn ($v) => $v !== null)
                    );
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Application status updated to ' . $validated['status'] . '.',
            'data'    => $application->fresh(),
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
