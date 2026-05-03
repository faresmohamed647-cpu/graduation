<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;

class AdminBusController extends Controller
{
    public function index(Request $request)
    {
        $query = Bus::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bus_number', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 25);
        $buses = $perPage === 'all' ? $query->get() : $query->paginate((int) $perPage);
        $items = $perPage === 'all' ? $buses : $buses->items();

        $mapped = collect($items)->map(fn (Bus $b) => [
            'id'           => $b->id,
            'bus_number'   => $b->bus_number,
            'plate_number' => $b->plate_number,
            'capacity'     => $b->capacity,
            'active'       => $b->active,
            'status'       => $b->active ? 'active' : 'inactive',
            'created_at'   => $b->created_at,
        ]);

        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function show(Bus $bus)
    {
        return response()->json(['success' => true, 'data' => $bus]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bus_number'   => ['required', 'string', 'unique:buses,bus_number'],
            'plate_number' => ['nullable', 'string', 'unique:buses,plate_number'],
            'capacity'     => ['required', 'integer', 'min:1'],
            'active'       => ['sometimes', 'boolean'],
        ]);

        $bus = Bus::create($data);

        return response()->json(['success' => true, 'data' => $bus, 'message' => 'Bus created'], 201);
    }

    public function update(Request $request, Bus $bus)
    {
        $data = $request->validate([
            'bus_number'   => ['sometimes', 'string', 'unique:buses,bus_number,' . $bus->id],
            'plate_number' => ['nullable', 'string', 'unique:buses,plate_number,' . $bus->id],
            'capacity'     => ['sometimes', 'integer', 'min:1'],
            'active'       => ['sometimes', 'boolean'],
        ]);

        $bus->update($data);

        return response()->json(['success' => true, 'data' => $bus, 'message' => 'Bus updated']);
    }

    public function destroy(Bus $bus)
    {
        $bus->delete();
        return response()->json(['success' => true, 'message' => 'Bus deleted']);
    }

    public function capacity()
    {
        $buses = Bus::withCount('trips')->get()->map(fn (Bus $b) => [
            'bus_number'       => $b->bus_number,
            'capacity'         => $b->capacity,
            'current_students' => 0,
        ]);

        return response()->json(['success' => true, 'data' => $buses]);
    }
}
