<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminParentController extends Controller
{
    public function index(Request $request)
    {
        $query = ParentProfile::with(['user', 'students']);

        if ($search = $request->get('search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $perPage = $request->get('per_page', 25);
        $parents = $perPage === 'all' ? $query->get() : $query->paginate((int) $perPage);
        $items = $perPage === 'all' ? $parents : $parents->items();

        $mapped = collect($items)->map(fn (ParentProfile $p) => [
            'id'               => $p->id,
            'name'             => $p->user?->name,
            'email'            => $p->user?->email,
            'password_plain'   => $p->user?->plain_password,
            'phone'            => $p->phone,
            'address'          => $p->address,
            'active'           => $p->active,
            'status'           => $p->active ? 'active' : 'inactive',
            'state'            => $p->state,
            'relationship'     => $p->relationship,
            'student_count'    => $p->student_count,
            'degree'           => $p->degree,
            'education_system' => $p->education_system,
            'school_name'      => $p->school_name,
            'school_address'   => $p->school_address,
            'school_starting'  => $p->school_starting,
            'message'          => $p->message,
            'students'         => $p->students->map(fn ($s) => ['id' => $s->id, 'name' => $s->full_name]),
            'created_at'       => $p->created_at,
        ]);

        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function show(ParentProfile $parent)
    {
        $parent->load(['user', 'students']);
        return response()->json(['success' => true, 'data' => $parent]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'password'         => ['sometimes', 'string', 'min:6'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'address'          => ['nullable', 'string', 'max:500'],
            'active'           => ['sometimes', 'boolean'],
            'state'            => ['nullable', 'string'],
            'relationship'     => ['nullable', 'string'],
            'student_count'    => ['nullable', 'integer'],
            'degree'           => ['nullable', 'string'],
            'education_system' => ['nullable', 'string'],
            'school_name'      => ['nullable', 'string'],
            'school_address'   => ['nullable', 'string'],
            'school_starting'  => ['nullable', 'string'],
            'message'          => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password'] ?? 'password'),
            'plain_password' => $data['password'] ?? 'password',
            'role'           => 'parent',
        ]);

        $parent = ParentProfile::create([
            'user_id'          => $user->id,
            'phone'            => $data['phone'] ?? null,
            'address'          => $data['address'] ?? null,
            'active'           => $data['active'] ?? true,
            'state'            => $data['state'] ?? null,
            'relationship'     => $data['relationship'] ?? null,
            'student_count'    => $data['student_count'] ?? 1,
            'degree'           => $data['degree'] ?? null,
            'education_system' => $data['education_system'] ?? null,
            'school_name'      => $data['school_name'] ?? null,
            'school_address'   => $data['school_address'] ?? null,
            'school_starting'  => $data['school_starting'] ?? null,
            'message'          => $data['message'] ?? null,
        ]);

        $parent->load(['user', 'students']);

        return response()->json(['success' => true, 'data' => $parent, 'message' => 'Parent created'], 201);
    }

    public function update(Request $request, ParentProfile $parent)
    {
        $data = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            'email'            => ['sometimes', 'email', 'unique:users,email,' . $parent->user_id],
            'phone'            => ['nullable', 'string', 'max:20'],
            'address'          => ['nullable', 'string', 'max:500'],
            'active'           => ['sometimes', 'boolean'],
            'state'            => ['nullable', 'string'],
            'relationship'     => ['nullable', 'string'],
            'student_count'    => ['nullable', 'integer'],
            'degree'           => ['nullable', 'string'],
            'education_system' => ['nullable', 'string'],
            'school_name'      => ['nullable', 'string'],
            'school_address'   => ['nullable', 'string'],
            'school_starting'  => ['nullable', 'string'],
            'message'          => ['nullable', 'string'],
        ]);

        if (isset($data['name']) || isset($data['email'])) {
            $parent->user->update(array_filter([
                'name'  => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ]));
        }

        $parent->update(array_filter([
            'phone'            => $data['phone'] ?? null,
            'address'          => $data['address'] ?? null,
            'active'           => $data['active'] ?? null,
            'state'            => $data['state'] ?? null,
            'relationship'     => $data['relationship'] ?? null,
            'student_count'    => $data['student_count'] ?? null,
            'degree'           => $data['degree'] ?? null,
            'education_system' => $data['education_system'] ?? null,
            'school_name'      => $data['school_name'] ?? null,
            'school_address'   => $data['school_address'] ?? null,
            'school_starting'  => $data['school_starting'] ?? null,
            'message'          => $data['message'] ?? null,
        ], fn ($v) => $v !== null));

        return response()->json(['success' => true, 'data' => $parent->fresh(['user', 'students']), 'message' => 'Parent updated']);
    }

    public function destroy(ParentProfile $parent)
    {
        $parent->delete();
        return response()->json(['success' => true, 'message' => 'Parent deleted']);
    }

    public function approve(ParentProfile $parent)
    {
        $parent->update(['active' => true]);
        return response()->json(['success' => true, 'message' => 'Parent approved']);
    }

    public function reject(ParentProfile $parent)
    {
        $parent->update(['active' => false]);
        return response()->json(['success' => true, 'message' => 'Parent rejected']);
    }
}
