<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['trip.driver.user', 'trip.bus', 'trip.route', 'student.parent.user'])
            ->latest('id');

        if ($tripId = $request->get('trip_id')) {
            $query->where('trip_id', $tripId);
        }

        if ($studentId = $request->get('student_id')) {
            $query->where('student_id', $studentId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $records = $query->limit((int) $request->get('limit', 100))->get();

        return response()->json(['success' => true, 'data' => $records]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'status' => ['sometimes', 'string', 'in:absent,picked_up,dropped_off,no_show'],
            'picked_up_at' => ['nullable', 'date'],
            'dropped_off_at' => ['nullable', 'date'],
        ]);

        $attendance = Attendance::updateOrCreate(
            ['trip_id' => $data['trip_id'], 'student_id' => $data['student_id']],
            [
                'status' => $data['status'] ?? 'absent',
                'picked_up_at' => $data['picked_up_at'] ?? null,
                'dropped_off_at' => $data['dropped_off_at'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $attendance->load(['trip', 'student']),
            'message' => 'Attendance saved',
        ], $attendance->wasRecentlyCreated ? 201 : 200);
    }

    public function show(Attendance $attendance)
    {
        return response()->json([
            'success' => true,
            'data' => $attendance->load(['trip.driver.user', 'trip.bus', 'trip.route', 'student.parent.user']),
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'status' => ['sometimes', 'string', 'in:absent,picked_up,dropped_off,no_show'],
            'picked_up_at' => ['nullable', 'date'],
            'dropped_off_at' => ['nullable', 'date'],
        ]);

        $attendance->update($data);

        return response()->json([
            'success' => true,
            'data' => $attendance->fresh(['trip', 'student']),
            'message' => 'Attendance updated',
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return response()->json(['success' => true, 'message' => 'Attendance deleted']);
    }
}
