<?php

namespace App\Http\Controllers;

use App\Services\AdminSubmissionNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApplicationController extends Controller
{
    /**
     * Submit a professional application (Admin, Driver, or Other).
     * Saves to the 'applications' table for review by the system owner.
     */
    public function submit(Request $request)
    {
        $request->merge([
            'role' => strtolower((string) $request->input('role')),
        ]);

        $data = $request->validate([
            'full_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['required', 'string', 'max:30'],
            'address'    => ['required', 'string', 'max:255'],
            'role'       => ['required', 'string', 'in:admin,driver,parent,other'],
            'experience' => ['required', 'string', 'max:2000'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $this->resolveApplicationUser($request, $data);

        $application = \App\Models\Application::create([
            'user_id'    => $user->id,
            'full_name'  => $data['full_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'address'    => $data['address'],
            'role'       => $data['role'],
            'experience' => $data['experience'],
            'notes'      => $data['notes'],
            'status'     => 'pending',
        ]);

        AdminSubmissionNotifier::notify(
            'application',
            'New application',
            ucfirst($data['role']) . ": {$data['full_name']}",
            ['id' => $application->id, 'role' => $data['role'], 'action' => 'applications']
        );

        if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'status' => 'success',
                'message' => 'Application submitted successfully!',
                'data' => $application,
            ]);
        }

        return back()->with('success', 'Application submitted successfully! We will review it and contact you soon.');
    }

    private function resolveApplicationUser(Request $request, array $data): \App\Models\User
    {
        $role = strtolower((string) $data['role']);
        $email = strtolower((string) $data['email']);
        $authUser = $request->user();

        if (
            $authUser
            && strtolower((string) $authUser->email) === $email
            && strtolower((string) $authUser->role) === $role
        ) {
            return $authUser;
        }

        $matchedUser = \App\Models\User::where('email', $data['email'])->first();
        if ($matchedUser) {
            if (strtolower((string) $matchedUser->role) !== $role && $matchedUser->role !== 'admin') {
                $matchedUser->update(['role' => $role]);
            }

            return $matchedUser;
        }

        $password = 'password123';

        return \App\Models\User::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'plain_password' => $password,
            'role' => $role,
        ]);
    }
}
