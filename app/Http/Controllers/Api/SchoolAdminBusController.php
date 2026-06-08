<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\MaintenanceRecord;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SchoolAdminBusController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $buses = Bus::where('school_id', $schoolId)
            ->with(['driver.user', 'route'])
            ->latest('id')
            ->get()
            ->map(fn (Bus $bus) => $this->mapBus($bus));

        return response()->json(['success' => true, 'data' => $buses]);
    }

    public function show(Request $request, Bus $bus)
    {
        $this->authorizeSchoolBus($request, $bus);
        $bus->load(['driver.user', 'route', 'trips' => fn ($q) => $q->latest()->limit(10)]);

        return response()->json(['success' => true, 'data' => $this->mapBus($bus)]);
    }

    public function store(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'bus_number' => ['required', 'string', 'max:50'],
            'plate_number' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'bus_route_id' => ['nullable', 'integer', 'exists:bus_routes,id'],
            'insurance_expiry' => ['nullable', 'date'],
            'active' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'in:active,maintenance,inactive'],
        ]);

        $data['school_id'] = $schoolId;
        $bus = Bus::create($data);
        $logger->log($request, 'bus.created', $bus);

        return response()->json(['success' => true, 'data' => $this->mapBus($bus->load(['driver.user', 'route'])), 'message' => 'Bus created'], 201);
    }

    public function update(Request $request, Bus $bus, ActivityLogService $logger)
    {
        $this->authorizeSchoolBus($request, $bus);
        $data = $request->validate([
            'bus_number' => ['sometimes', 'string', 'max:50'],
            'plate_number' => ['sometimes', 'string', 'max:50'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'bus_route_id' => ['nullable', 'integer', 'exists:bus_routes,id'],
            'insurance_expiry' => ['nullable', 'date'],
            'active' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'in:active,maintenance,inactive'],
            'documents' => ['nullable', 'array'],
        ]);

        $bus->update($data);
        $logger->log($request, 'bus.updated', $bus);

        return response()->json(['success' => true, 'data' => $this->mapBus($bus->fresh(['driver.user', 'route'])), 'message' => 'Bus updated']);
    }

    public function destroy(Request $request, Bus $bus, ActivityLogService $logger)
    {
        $this->authorizeSchoolBus($request, $bus);
        $bus->delete();
        $logger->log($request, 'bus.deleted', null, ['bus_id' => $bus->id]);

        return response()->json(['success' => true, 'message' => 'Bus deleted']);
    }

    public function maintenance(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $busIds = Bus::where('school_id', $schoolId)->pluck('id');

        $records = MaintenanceRecord::with('bus:id,bus_number')
            ->whereIn('bus_id', $busIds)
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $records]);
    }

    public function storeMaintenance(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'bus_id' => ['required', 'integer', 'exists:buses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'maintenance_date' => ['nullable', 'date'],
        ]);

        $bus = Bus::findOrFail($data['bus_id']);
        $this->authorizeSchoolBus($request, $bus);

        $record = MaintenanceRecord::create($data);
        $logger->log($request, 'maintenance.created', $record);

        return response()->json(['success' => true, 'data' => $record->load('bus'), 'message' => 'Maintenance record created'], 201);
    }

    private function authorizeSchoolBus(Request $request, Bus $bus): void
    {
        abort_unless((int) $bus->school_id === $this->schoolId($request), 403);
    }

    private function mapBus(Bus $bus): array
    {
        return [
            'id' => $bus->id,
            'bus_number' => $bus->bus_number,
            'plate_number' => $bus->plate_number,
            'capacity' => $bus->capacity,
            'active' => $bus->active,
            'status' => $bus->status ?? ($bus->active ? 'active' : 'inactive'),
            'insurance_expiry' => $bus->insurance_expiry?->toDateString(),
            'insurance_alert' => $bus->insurance_expiry && $bus->insurance_expiry->lte(now()->addDays(30)),
            'documents' => $bus->documents,
            'driver_id' => $bus->driver_id,
            'bus_route_id' => $bus->bus_route_id,
            'driver' => $bus->driver?->user?->name,
            'route' => $bus->route?->name,
            'current_lat' => $bus->current_lat,
            'current_lng' => $bus->current_lng,
        ];
    }
}
