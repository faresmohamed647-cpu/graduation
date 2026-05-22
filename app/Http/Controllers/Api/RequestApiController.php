<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\AdminSubmissionNotifier;
use Illuminate\Http\Request;

class RequestApiController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'request_type' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'context' => ['nullable', 'array'],
        ]);

        $report = Report::create([
            'user_id' => $request->user()->id,
            'trip_id' => null,
            'type' => $data['request_type'],
            'title' => $data['subject'],
            'body' => json_encode([
                'description' => $data['description'],
                'context' => $data['context'] ?? [],
            ], JSON_UNESCAPED_UNICODE),
            'status' => 'open',
        ]);

        AdminSubmissionNotifier::notify(
            'report',
            'New report / request',
            $data['subject'],
            ['id' => $report->id, 'action' => 'reports']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Request submitted successfully.',
            'data' => $report,
        ], 201);
    }
}
