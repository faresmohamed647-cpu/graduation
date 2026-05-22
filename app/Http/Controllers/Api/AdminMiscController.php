<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\FinancialEntry;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;

class AdminMiscController extends Controller
{
    public function indexSchools()
    {
        return response()->json(['success' => true, 'data' => School::latest()->get()]);
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
}
