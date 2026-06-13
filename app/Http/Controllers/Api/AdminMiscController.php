<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\FinancialEntry;
use App\Models\MaintenanceRecord;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class AdminMiscController extends Controller
{
    public function indexSchools()
    {
        return response()->json(['success' => true, 'data' => School::latest()->get()]);
    }

    public function pendingProfiles()
    {
        $schools = School::with(['administrators' => function ($query) {
            $query->select('id', 'name', 'email', 'school_id');
        }])
            ->where('status', 'pending_approval')
            ->orderByDesc('profile_submitted_at')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json(['success' => true, 'data' => $schools]);
    }

    public function showSchoolProfile(School $school)
    {
        $school->load(['administrators' => function ($query) {
            $query->select('id', 'name', 'email', 'school_id');
        }]);

        return response()->json(['success' => true, 'data' => $school]);
    }

    public function indexFinancialEntries()
    {
        return response()->json(['success' => true, 'data' => FinancialEntry::latest()->get()]);
    }

    public function indexMaintenanceRecords()
    {
        return response()->json([
            'success' => true,
            'data'    => MaintenanceRecord::with('bus:id,bus_number')->latest()->get(),
        ]);
    }

    public function storeSchool(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string',
            'email'   => 'nullable|email',
            'notes'   => 'nullable|string',
        ]);
        $school = School::create($data);
        return response()->json(['success' => true, 'data' => $school, 'message' => 'School created']);
    }

    public function storeFinancialEntry(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'entry_date'  => 'nullable|date',
        ]);
        $entry = FinancialEntry::create($data);
        return response()->json(['success' => true, 'data' => $entry, 'message' => 'Financial entry created']);
    }

    public function storeMaintenanceRecord(Request $request)
    {
        $data = $request->validate([
            'bus_id'           => 'nullable|exists:buses,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'cost'             => 'nullable|numeric|min:0',
            'status'           => 'required|in:pending,in_progress,completed',
            'maintenance_date' => 'nullable|date',
        ]);
        $record = MaintenanceRecord::create($data);
        return response()->json(['success' => true, 'data' => $record, 'message' => 'Maintenance record created']);
    }

    public function approveSchool(School $school, Request $request, ActivityLogService $logger)
    {
        if ($school->status !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Only schools awaiting profile approval can be approved.',
            ], 422);
        }

        $school->update(['active' => true, 'status' => 'active']);

        $logger->log($request, 'school_profile_approved', $school, [
            'school_name' => $school->name,
        ]);

        return response()->json(['success' => true, 'message' => 'School profile approved and dashboard unlocked.']);
    }

    public function rejectSchool(School $school, Request $request, ActivityLogService $logger)
    {
        if (! in_array($school->status, ['pending_approval', 'pending_details'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This school cannot be rejected in its current state.',
            ], 422);
        }

        $school->update(['active' => false, 'status' => 'rejected']);

        $logger->log($request, 'school_profile_rejected', $school, [
            'school_name' => $school->name,
        ]);

        return response()->json(['success' => true, 'message' => 'School profile rejected.']);
    }
}
