<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user:id,name', 'school:id,name'])
            ->latest();

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->integer('school_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->get('action') . '%');
        }

        $logs = $query->paginate(min((int) $request->get('per_page', 50), 200));

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}
