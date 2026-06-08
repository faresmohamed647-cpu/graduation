<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\EmergencyAlert;
use App\Models\User;
use App\Notifications\SchoolAnnouncementNotification;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SchoolAdminEmergencyController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $alerts = EmergencyAlert::where('school_id', $schoolId)
            ->with(['trip', 'student', 'driver.user', 'reporter'])
            ->latest()
            ->paginate((int) $request->get('per_page', 25));

        return response()->json(['success' => true, 'data' => $alerts->items(), 'meta' => [
            'current_page' => $alerts->currentPage(),
            'total' => $alerts->total(),
        ]]);
    }

    public function store(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'type' => ['required', 'string', 'in:sos,breakdown,student_emergency,driver_report,weather'],
            'severity' => ['sometimes', 'string', 'in:low,medium,high,critical'],
            'message' => ['required', 'string'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $alert = EmergencyAlert::create([
            ...$data,
            'school_id' => $schoolId,
            'reported_by' => $request->user()->id,
            'status' => 'open',
        ]);

        User::where('role', 'admin')->each(function (User $admin) use ($alert) {
            $admin->notify(new SchoolAnnouncementNotification(
                'Emergency Alert: ' . $alert->type,
                $alert->message ?? 'Emergency reported at school.',
                'emergency',
            ));
        });

        $logger->log($request, 'emergency.created', $alert);

        return response()->json(['success' => true, 'data' => $alert, 'message' => 'Emergency alert created'], 201);
    }

    public function resolve(Request $request, EmergencyAlert $alert, ActivityLogService $logger)
    {
        abort_unless((int) $alert->school_id === $this->schoolId($request), 403);

        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        $logger->log($request, 'emergency.resolved', $alert);

        return response()->json(['success' => true, 'data' => $alert, 'message' => 'Emergency resolved']);
    }
}
