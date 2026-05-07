<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminApplicationController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Application::latest()->limit(100)->get(),
        ]);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'accepted', 'rejected'])],
        ]);

        $application->update(['status' => $data['status']]);

        return response()->json([
            'status' => 'success',
            'data' => $application->fresh(),
        ]);
    }
}
