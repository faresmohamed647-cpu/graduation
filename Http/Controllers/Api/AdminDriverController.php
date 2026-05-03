<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with('user');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->get('per_page', 25);
        $drivers = $perPage === 'all' ? $query->get() : $query->paginate((int) $perPage);
        $items = $perPage === 'all' ? $drivers : $drivers->items();

        $mapped = collect($items)->map(fn (Driver $d) => [
            'id'               => $d->id,
            'name'             => $d->user?->name,
            'email'            => $d->user?->email,
            'password_plain'   => $d->user?->plain_password,
            'phone'            => $d->phone,
            'license_number'   => $d->license_number,
            'years_experience' => $d->years_experience,
            'active'           => $d->active,
            'status'           => $d->status,
            'interview_date'   => $d->interview_date,
            'state'            => $d->state,
            'full_name'        => $d->full_name,
            'age'              => $d->age,
            'gender'           => $d->gender,
            'car_type'         => $d->car_type,
            'car_model'        => $d->car_model,
            'car_plate'        => $d->car_plate,
            'address'          => $d->address,
            'message'          => $d->message,
            'created_at'       => $d->created_at,
        ]);

        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function show(Driver $driver)
    {
        $driver->load('user');
        return response()->json(['success' => true, 'data' => $driver]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'password'         => ['sometimes', 'string', 'min:6'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'license_number'   => ['nullable', 'string', 'max:50'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'active'           => ['sometimes', 'boolean'],
            'state'            => ['nullable', 'string'],
            'full_name'        => ['nullable', 'string'],
            'age'              => ['nullable', 'integer'],
            'gender'           => ['nullable', 'string'],
            'car_type'         => ['nullable', 'string'],
            'car_model'        => ['nullable', 'string'],
            'car_plate'        => ['nullable', 'string'],
            'address'          => ['nullable', 'string'],
            'message'          => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password'] ?? 'password'),
            'plain_password' => $data['password'] ?? 'password',
            'role'           => 'driver',
        ]);

        $driver = Driver::create([
            'user_id'          => $user->id,
            'phone'            => $data['phone'] ?? null,
            'license_number'   => $data['license_number'] ?? null,
            'years_experience' => $data['years_experience'] ?? null,
            'active'           => $data['active'] ?? true,
            'status'           => ($data['active'] ?? true) ? 'approved' : 'pending',
            'state'            => $data['state'] ?? null,
            'full_name'        => $data['full_name'] ?? $data['name'],
            'age'              => $data['age'] ?? null,
            'gender'           => $data['gender'] ?? null,
            'car_type'         => $data['car_type'] ?? null,
            'car_model'        => $data['car_model'] ?? null,
            'car_plate'        => $data['car_plate'] ?? null,
            'address'          => $data['address'] ?? null,
            'message'          => $data['message'] ?? null,
        ]);

        $driver->load('user');

        return response()->json(['success' => true, 'data' => $driver, 'message' => 'Driver created'], 201);
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            'email'            => ['sometimes', 'email', 'unique:users,email,' . $driver->user_id],
            'phone'            => ['nullable', 'string', 'max:20'],
            'license_number'   => ['nullable', 'string', 'max:50'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'active'           => ['sometimes', 'boolean'],
            'state'            => ['nullable', 'string'],
            'full_name'        => ['nullable', 'string'],
            'age'              => ['nullable', 'integer'],
            'gender'           => ['nullable', 'string'],
            'car_type'         => ['nullable', 'string'],
            'car_model'        => ['nullable', 'string'],
            'car_plate'        => ['nullable', 'string'],
            'address'          => ['nullable', 'string'],
            'message'          => ['nullable', 'string'],
        ]);

        if (isset($data['name']) || isset($data['email'])) {
            $driver->user->update(array_filter([
                'name'  => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ]));
        }

        $driver->update(array_merge(
            array_filter([
                'phone'            => $data['phone'] ?? null,
                'license_number'   => $data['license_number'] ?? null,
                'years_experience' => $data['years_experience'] ?? null,
                'active'           => $data['active'] ?? null,
                'state'            => $data['state'] ?? null,
                'full_name'        => $data['full_name'] ?? null,
                'age'              => $data['age'] ?? null,
                'gender'           => $data['gender'] ?? null,
                'car_type'         => $data['car_type'] ?? null,
                'car_model'        => $data['car_model'] ?? null,
                'car_plate'        => $data['car_plate'] ?? null,
                'address'          => $data['address'] ?? null,
                'message'          => $data['message'] ?? null,
            ], fn ($v) => $v !== null),
            ['status' => isset($data['active']) ? ($data['active'] ? 'approved' : 'rejected') : $driver->status]
        ));

        return response()->json(['success' => true, 'data' => $driver->fresh('user'), 'message' => 'Driver updated']);
    }

    public function destroy(Driver $driver)
    {
        $driver->user->delete();
        return response()->json(['success' => true, 'message' => 'Driver deleted']);
    }

    public function pending()
    {
        $drivers = Driver::with('user')->whereIn('status', ['pending', 'interview_scheduled'])->get();
        return response()->json(['success' => true, 'data' => $drivers]);
    }

    public function approve(Driver $driver)
    {
        $driver->update(['active' => true, 'status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'Driver approved']);
    }

    public function reject(Driver $driver)
    {
        $driver->update(['active' => false, 'status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'Driver rejected']);
    }
}
