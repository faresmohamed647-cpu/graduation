<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use Illuminate\Http\Request;

class AdminRouteController extends Controller
{
    public function index(Request $request)
    {
        $query = BusRoute::query();
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        $routes = $query->get();
        $mapped = $routes->map(fn (BusRoute $r) => [
            'id' => $r->id, 'name' => $r->name, 'type' => $r->type,
            'stops' => $r->stops, 'estimated_minutes' => $r->estimated_minutes,
            'active' => $r->active, 'created_at' => $r->created_at,
        ]);
        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function show(BusRoute $route)
    {
        return response()->json(['success' => true, 'data' => $route]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:morning,afternoon,custom'],
            'stops' => ['nullable', 'array'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'active' => ['sometimes', 'boolean'],
        ]);
        $route = BusRoute::create($data);
        return response()->json(['success' => true, 'data' => $route, 'message' => 'Route created'], 201);
    }

    public function update(Request $request, BusRoute $route)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:morning,afternoon,custom'],
            'stops' => ['nullable', 'array'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'active' => ['sometimes', 'boolean'],
        ]);
        $route->update($data);
        return response()->json(['success' => true, 'data' => $route, 'message' => 'Route updated']);
    }

    public function destroy(BusRoute $route)
    {
        $route->delete();
        return response()->json(['success' => true, 'message' => 'Route deleted']);
    }

    public function addStop(Request $request, BusRoute $route)
    {
        $data = $request->validate(['name' => ['required', 'string'], 'lat' => ['nullable', 'numeric'], 'lng' => ['nullable', 'numeric'], 'order' => ['nullable', 'integer']]);
        $stops = $route->stops ?? [];
        $stops[] = $data;
        $route->update(['stops' => $stops]);
        return response()->json(['success' => true, 'data' => $route, 'message' => 'Stop added']);
    }
}
