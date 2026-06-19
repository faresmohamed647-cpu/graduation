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
        $query = User::with([
            'school:id,name,email,phone,address',
            'driverProfile',
            'parentProfile.students:id,parent_id,full_name,grade,school_name',
        ]);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()->get();

        $mapped = $users->map(function (User $u) {
            $phone = null;
            $status = 'active';
            $profileDetails = [];

            if ($u->role === 'parent' && $u->parentProfile) {
                $parent = $u->parentProfile;
                $phone = $parent->phone;
                $status = $parent->active ? 'active' : 'inactive';
                $profileDetails = array_filter([
                    'phone' => $parent->phone,
                    'address' => $parent->address,
                    'relationship' => $parent->relationship,
                    'student_count' => $parent->student_count,
                    'school_name' => $parent->school_name,
                    'children' => $parent->students->map(fn ($s) => [
                        'id' => $s->id,
                        'name' => $s->full_name,
                        'grade' => $s->grade,
                        'school' => $s->school_name,
                    ])->values()->all(),
                ], fn ($v) => $v !== null && $v !== '' && $v !== []);
            } elseif ($u->role === 'driver' && $u->driverProfile) {
                $driver = $u->driverProfile;
                $phone = $driver->phone;
                $status = $driver->active ? ($driver->status ?? 'active') : 'inactive';
                $profileDetails = array_filter([
                    'phone' => $driver->phone,
                    'license_number' => $driver->license_number,
                    'years_experience' => $driver->years_experience,
                    'car_type' => $driver->car_type,
                    'car_model' => $driver->car_model,
                    'car_plate' => $driver->car_plate,
                    'address' => $driver->address,
                    'status' => $driver->status,
                ], fn ($v) => $v !== null && $v !== '');
            } elseif ($u->role === 'school_admin') {
                $phone = $u->school?->phone;
                $profileDetails = array_filter([
                    'school_name' => $u->school?->name,
                    'school_email' => $u->school?->email,
                    'school_phone' => $u->school?->phone,
                    'school_address' => $u->school?->address,
                ], fn ($v) => $v !== null && $v !== '');
            }

            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'password_plain' => $u->plain_password,
                'roles' => [$u->role],
                'role' => $u->role,
                'school_id' => $u->school_id,
                'school_name' => $u->school?->name,
                'phone' => $phone,
                'status' => $status,
                'profile_details' => $profileDetails,
                'children_count' => $u->parentProfile?->students?->count() ?? 0,
                'created_at' => $u->created_at?->toIso8601String(),
                'updated_at' => $u->updated_at?->toIso8601String(),
            ];
        });

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
