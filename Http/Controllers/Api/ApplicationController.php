<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'address'    => ['required', 'string', 'max:255'],
            'role'       => ['required', 'string', 'in:Driver,Parent,Other'],
            'experience' => ['required', 'string'],
            'notes'      => ['nullable', 'string'],
        ]);

        $application = Application::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Your application has been submitted successfully!',
            'data'    => $application,
        ], 201);
    }
}
