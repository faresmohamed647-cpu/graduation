<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\AdminSubmissionNotifier;
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

        AdminSubmissionNotifier::notify(
            'service_request',
            'New service request',
            "{$role}: {$data['subject']}",
            ['id' => $serviceRequest->id, 'role' => $role, 'action' => 'requests']
        );

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

        $oldStatus = $serviceRequest->status;
        $serviceRequest->update($updateData);

        if ($serviceRequest->role === 'school_admin' && $data['status'] === 'resolved' && $oldStatus !== 'resolved') {
            $this->approveSchoolRequest($serviceRequest);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Request updated successfully.',
            'data' => $serviceRequest->fresh(['user', 'handler']),
        ]);
    }

    /**
     * Helper to approve school request and create database entries.
     */
    private function approveSchoolRequest(ServiceRequest $serviceRequest)
    {
        $type = $serviceRequest->request_type;
        $meta = $serviceRequest->metadata;
        $schoolId = $serviceRequest->user->school_id ?? null;

        if (is_string($meta)) {
            $meta = json_decode($meta, true) ?: [];
        }

        switch ($type) {
            case 'add_student':
                \App\Models\Student::create([
                    'school_id' => $schoolId,
                    'parent_id' => $meta['parent_id'] ?? null,
                    'full_name' => $meta['full_name'] ?? $meta['name'] ?? null,
                    'grade' => $meta['grade'] ?? null,
                    'bus_id' => $meta['bus_id'] ?? null,
                    'bus_route_id' => $meta['bus_route_id'] ?? null,
                    'qr_code' => 'QR-' . rand(100, 999),
                    'rfid_tag' => 'RFID-' . rand(100, 999),
                    'active' => true,
                ]);
                break;
            case 'add_bus':
                \App\Models\Bus::create([
                    'school_id' => $schoolId,
                    'bus_number' => $meta['bus_number'] ?? null,
                    'plate_number' => $meta['plate_number'] ?? null,
                    'capacity' => $meta['capacity'] ?? null,
                    'insurance_expiry' => now()->addYear(),
                    'status' => 'active',
                    'active' => true,
                ]);
                break;
            case 'add_route':
                \App\Models\BusRoute::create([
                    'school_id' => $schoolId,
                    'name' => $meta['name'] ?? null,
                    'type' => $meta['type'] ?? 'morning',
                    'estimated_minutes' => $meta['estimated_minutes'] ?? 30,
                    'stops' => $meta['stops'] ?? [],
                    'distance_km' => 5.0,
                    'active' => true,
                ]);
                break;
            case 'add_trip':
                \App\Models\Trip::create([
                    'school_id' => $schoolId,
                    'trip_date' => $meta['trip_date'] ?? null,
                    'shift' => $meta['shift'] ?? 'morning',
                    'driver_id' => $meta['driver_id'] ?? null,
                    'bus_id' => $meta['bus_id'] ?? null,
                    'bus_route_id' => $meta['bus_route_id'] ?? null,
                    'status' => 'active',
                ]);
                break;
        }
    }

    /**
     * Public: Store a quote request from website (no auth required).
     */
    public function storePublic(Request $request)
    {
        $data = $request->validate([
            'request_type' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'notes' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description_extra' => ['nullable', 'string'],
        ]);

        $description = $data['description'];
        if (! empty($data['phone'])) {
            $description = 'Phone: ' . $data['phone'] . "\n" . $description;
        }
        if (! empty($data['email'])) {
            $description = 'Email: ' . $data['email'] . "\n" . $description;
        }
        if (! empty($data['description_extra'])) {
            $description .= "\n\nNote: " . $data['description_extra'];
        }

        $serviceRequest = ServiceRequest::create([
            'user_id' => null,
            'role' => 'guest',
            'request_type' => $data['request_type'],
            'subject' => $data['subject'],
            'description' => $description,
            'priority' => $data['priority'] ?? 'medium',
            'notes' => $data['notes'] ?? $data['email'] ?? null,
            'status' => 'pending',
        ]);

        AdminSubmissionNotifier::notify(
            'service_request',
            'New quote request',
            "Guest: {$data['subject']}",
            ['id' => $serviceRequest->id, 'role' => 'guest', 'action' => 'requests']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Quote request submitted successfully.',
            'data' => $serviceRequest,
        ], 201);
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
