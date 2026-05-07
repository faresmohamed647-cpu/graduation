<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceRequestController extends Controller
{
    /**
     * Store a new service request (for parent or driver).
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $role = strtolower($user->role ?? 'parent');

        // Validate based on role
        $rules = [
            'request_type' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $serviceRequest = ServiceRequest::create([
            'user_id' => $user->id,
            'role' => $role,
            'request_type' => $data['request_type'],
            'subject' => $data['subject'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? 'medium',
            'notes' => $data['notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Request submitted successfully.',
            'data' => $serviceRequest,
        ], 201);
    }

    /**
     * Get current user's requests.
     */
    public function myRequests(Request $request)
    {
        $user = $request->user();

        $requests = ServiceRequest::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'request_type' => $r->request_type,
                'subject' => $r->subject,
                'description' => $r->description,
                'priority' => $r->priority,
                'status' => $r->status,
                'notes' => $r->notes,
                'metadata' => $r->metadata,
                'admin_response' => $r->admin_response,
                'created_at' => $r->created_at->toIso8601String(),
                'updated_at' => $r->updated_at->toIso8601String(),
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $requests,
        ]);
    }

    /**
     * Admin: Get all requests with optional filters.
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['user:id,name,email,role', 'handler:id,name'])
            ->latest();

        if ($request->has('role')) {
            $query->forRole($request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'status' => 'success',
            'data' => $requests,
        ]);
    }

    /**
     * Admin: Get single request details.
     */
    public function show(Request $request, ServiceRequest $serviceRequest)
    {
        return response()->json([
            'status' => 'success',
            'data' => $serviceRequest->load(['user', 'handler']),
        ]);
    }

    /**
     * Admin: Update request status and add response.
     */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,in-progress,resolved,rejected'],
            'admin_response' => ['nullable', 'string'],
        ]);

        $updateData = [
            'status' => $data['status'],
            'admin_response' => $data['admin_response'] ?? $serviceRequest->admin_response,
        ];

        if (in_array($data['status'], ['resolved', 'rejected'])) {
            $updateData['handled_by'] = $request->user()->id;
            $updateData['handled_at'] = now();
        }

        $serviceRequest->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Request updated successfully.',
            'data' => $serviceRequest->fresh(['user', 'handler']),
        ]);
    }

    /**
     * Admin: Get requests statistics.
     */
    public function stats(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => ServiceRequest::count(),
                'pending' => ServiceRequest::pending()->count(),
                'in_progress' => ServiceRequest::where('status', 'in-progress')->count(),
                'resolved' => ServiceRequest::where('status', 'resolved')->count(),
                'rejected' => ServiceRequest::where('status', 'rejected')->count(),
                'by_role' => [
                    'parent' => ServiceRequest::forRole('parent')->count(),
                    'driver' => ServiceRequest::forRole('driver')->count(),
                ],
                'by_priority' => [
                    'high' => ServiceRequest::where('priority', 'high')->count(),
                    'medium' => ServiceRequest::where('priority', 'medium')->count(),
                    'low' => ServiceRequest::where('priority', 'low')->count(),
                ],
            ],
        ]);
    }
}
