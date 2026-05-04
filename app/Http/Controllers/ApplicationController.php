<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    /**
     * Submit a professional application (Admin, Driver, or Other).
     * Saves to the 'applications' table for review by the system owner.
     */
    public function submit(Request $request)
    {
        $data = $request->validate([
            'full_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['required', 'string', 'max:30'],
            'address'    => ['required', 'string', 'max:255'],
            'role'       => ['required', 'string', 'in:Admin,Driver,Parent,Other'],
            'experience' => ['required', 'string', 'max:2000'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        \App\Models\Application::create([
            'user_id'    => auth()->id(),
            'full_name'  => $data['full_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'address'    => $data['address'],
            'role'       => $data['role'],
            'experience' => $data['experience'],
            'notes'      => $data['notes'],
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Application submitted successfully! We will review it and contact you soon.');
    }
}
