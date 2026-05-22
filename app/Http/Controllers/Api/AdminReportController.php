<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\AdminSubmissionNotifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::with('user:id,name,email')
            ->latest()
            ->limit(200)
            ->get();

        return response()->json(['success' => true, 'data' => $reports]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'   => ['nullable', 'string', 'max:50'],
            'title'  => ['required', 'string', 'max:255'],
            'body'   => ['required', 'string'],
            'status' => ['nullable', Rule::in(['open', 'resolved', 'closed'])],
        ]);

        $report = Report::create([
            'user_id' => $request->user()->id,
            'trip_id' => null,
            'type'    => $data['type'] ?? 'complaint',
            'title'   => $data['title'],
            'body'    => $data['body'],
            'status'  => $data['status'] ?? 'open',
        ]);

        AdminSubmissionNotifier::notify(
            'report',
            'New complaint logged',
            $data['title'],
            ['id' => $report->id, 'action' => 'complaints']
        );

        return response()->json([
            'success' => true,
            'data'    => $report->load('user:id,name,email'),
            'message' => 'Report created',
        ], 201);
    }

    public function update(Request $request, Report $report)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['open', 'resolved', 'closed', 'in-progress'])],
            'title'  => ['sometimes', 'string', 'max:255'],
            'body'   => ['sometimes', 'string'],
        ]);

        $report->update($data);

        return response()->json([
            'success' => true,
            'data'    => $report->fresh('user:id,name,email'),
            'message' => 'Report updated',
        ]);
    }
}
