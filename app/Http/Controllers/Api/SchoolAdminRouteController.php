<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SchoolAdminRouteController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $routes = BusRoute::where('school_id', $schoolId)
            ->with(['bus', 'driver.user', 'students'])
            ->latest('id')
            ->get()
            ->map(fn (BusRoute $route) => $this->mapRoute($route));

        return response()->json(['success' => true, 'data' => $routes]);
    }

    public function show(Request $request, BusRoute $route)
    {
        $this->authorizeSchoolRoute($request, $route);
        $route->load(['bus', 'driver.user', 'students.parent.user']);

        return response()->json(['success' => true, 'data' => $this->mapRoute($route)]);
    }

    public function store(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:morning,afternoon,evening'],
            'stops' => ['required', 'array', 'min:1'],
            'stops.*.name' => ['required', 'string'],
            'stops.*.lat' => ['required', 'numeric'],
            'stops.*.lng' => ['required', 'numeric'],
            'stops.*.order' => ['required', 'integer'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $data['school_id'] = $schoolId;
        $route = BusRoute::create($data);
        $logger->log($request, 'route.created', $route);

        return response()->json(['success' => true, 'data' => $this->mapRoute($route), 'message' => 'Route created'], 201);
    }

    public function update(Request $request, BusRoute $route, ActivityLogService $logger)
    {
        $this->authorizeSchoolRoute($request, $route);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:morning,afternoon,evening'],
            'stops' => ['sometimes', 'array', 'min:1'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $route->update($data);
        $logger->log($request, 'route.updated', $route);

        return response()->json(['success' => true, 'data' => $this->mapRoute($route->fresh(['bus', 'driver.user', 'students'])), 'message' => 'Route updated']);
    }

    public function destroy(Request $request, BusRoute $route, ActivityLogService $logger)
    {
        $this->authorizeSchoolRoute($request, $route);
        $route->delete();
        $logger->log($request, 'route.deleted', null, ['route_id' => $route->id]);

        return response()->json(['success' => true, 'message' => 'Route deleted']);
    }

    public function addStop(Request $request, BusRoute $route, ActivityLogService $logger)
    {
        $this->authorizeSchoolRoute($request, $route);
        $stop = $request->validate([
            'name' => ['required', 'string'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'order' => ['required', 'integer'],
        ]);

        $stops = $route->stops ?? [];
        $stops[] = $stop;
        $route->update(['stops' => $stops]);
        $logger->log($request, 'route.stop_added', $route, $stop);

        return response()->json(['success' => true, 'data' => $this->mapRoute($route), 'message' => 'Stop added']);
    }

    private function authorizeSchoolRoute(Request $request, BusRoute $route): void
    {
        abort_unless((int) $route->school_id === $this->schoolId($request), 403);
    }

    private function mapRoute(BusRoute $route): array
    {
        return [
            'id' => $route->id,
            'name' => $route->name,
            'type' => $route->type,
            'stops' => $route->stops,
            'estimated_minutes' => $route->estimated_minutes,
            'distance_km' => $route->distance_km,
            'active' => $route->active,
            'bus_id' => $route->bus_id,
            'driver_id' => $route->driver_id,
            'bus' => $route->bus?->bus_number,
            'driver' => $route->driver?->user?->name,
            'students_count' => $route->students?->count() ?? 0,
            'students' => $route->students?->map(fn ($s) => ['id' => $s->id, 'name' => $s->full_name])->values(),
        ];
    }
}
