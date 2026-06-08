<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($role = $request->get('role')) { $query->where('role', $role); }
        $users = $query->latest()->get();
        $mapped = $users->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'password_plain' => $u->plain_password, // For admin visibility
            'roles' => [$u->role],
            'role' => $u->role,
            'status' => 'active',
            'created_at' => $u->created_at,
        ]);
        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['sometimes', 'string', 'min:6'],
            'role' => ['sometimes', 'string', 'in:admin,driver,parent,school_admin'],
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
        ]);
        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password'] ?? 'password'),
            'plain_password' => $data['password'] ?? 'password',
            'role'           => $data['role'] ?? 'parent',
            'school_id'      => $data['school_id'] ?? null,
        ]);
        return response()->json(['success' => true, 'data' => $user, 'message' => 'User created'], 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
            'role' => ['sometimes', 'string', 'in:admin,driver,parent,school_admin'],
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
        ]);
        $user->update($data);
        return response()->json(['success' => true, 'data' => $user, 'message' => 'User updated']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted']);
    }

    public function roles()
    {
        return response()->json(['success' => true, 'data' => ['admin', 'school_admin', 'driver', 'parent']]);
    }
}
