<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolAdminStudentController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $query = Student::with(['parent.user', 'bus', 'route', 'school'])
            ->where('school_id', $schoolId)
            ->latest('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('grade', 'like', "%{$search}%")
                    ->orWhere('qr_code', 'like', "%{$search}%")
                    ->orWhere('rfid_tag', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->get('status') === 'active');
        }

        $perPage = $request->get('per_page', 25);
        $students = $perPage === 'all' ? $query->get() : $query->paginate((int) $perPage);
        $items = $perPage === 'all' ? $students : $students->items();

        $mapped = collect($items)->map(fn (Student $s) => $this->mapStudent($s));

        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function show(Request $request, Student $student)
    {
        $this->authorizeSchoolStudent($request, $student);
        $student->load(['parent.user', 'bus.driver.user', 'route', 'attendance.trip']);

        $stats = [
            'total_trips' => $student->attendance()->count(),
            'picked_up' => $student->attendance()->where('status', 'picked_up')->count(),
            'dropped_off' => $student->attendance()->where('status', 'dropped_off')->count(),
            'absent' => $student->attendance()->where('status', 'absent')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => array_merge($this->mapStudent($student)->toArray(), [
                'attendance_stats' => $stats,
                'attendance_history' => $student->attendance->sortByDesc('created_at')->values(),
            ]),
        ]);
    }

    public function store(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'parent_id' => ['required', 'integer', 'exists:parents,id'],
            'grade' => ['nullable', 'string', 'max:50'],
            'active' => ['sometimes', 'boolean'],
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'bus_route_id' => ['nullable', 'integer', 'exists:bus_routes,id'],
            'rfid_tag' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $this->validateSchoolResources($request, $data);
        $school = $this->school($request);
        $data['school_id'] = $schoolId;
        $data['school_name'] = $school->name;
        $data['qr_code'] = 'QR-' . strtoupper(Str::random(8));

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student = Student::create($data);
        $logger->log($request, 'student.created', $student);

        return response()->json([
            'success' => true,
            'data' => $this->mapStudent($student->load(['parent.user', 'bus', 'route'])),
            'message' => 'Student created',
        ], 201);
    }

    public function update(Request $request, Student $student, ActivityLogService $logger)
    {
        $this->authorizeSchoolStudent($request, $student);

        $data = $request->validate([
            'full_name' => ['sometimes', 'string', 'max:255'],
            'parent_id' => ['sometimes', 'integer', 'exists:parents,id'],
            'grade' => ['nullable', 'string', 'max:50'],
            'active' => ['sometimes', 'boolean'],
            'bus_id' => ['nullable', 'integer', 'exists:buses,id'],
            'bus_route_id' => ['nullable', 'integer', 'exists:bus_routes,id'],
            'rfid_tag' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $this->validateSchoolResources($request, $data);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student->update($data);
        $logger->log($request, 'student.updated', $student);

        return response()->json([
            'success' => true,
            'data' => $this->mapStudent($student->fresh(['parent.user', 'bus', 'route'])),
            'message' => 'Student updated',
        ]);
    }

    public function destroy(Request $request, Student $student, ActivityLogService $logger)
    {
        $this->authorizeSchoolStudent($request, $student);
        $student->delete();
        $logger->log($request, 'student.deleted', null, ['student_id' => $student->id]);

        return response()->json(['success' => true, 'message' => 'Student deleted']);
    }

    public function qrCode(Request $request, Student $student)
    {
        $this->authorizeSchoolStudent($request, $student);

        if (! $student->qr_code) {
            $student->update(['qr_code' => 'QR-' . strtoupper(Str::random(8))]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student_id' => $student->id,
                'name' => $student->full_name,
                'qr_code' => $student->qr_code,
            ],
        ]);
    }

    private function validateSchoolResources(Request $request, array $data): void
    {
        $schoolId = $this->schoolId($request);

        if (! empty($data['bus_id'])) {
            abort_unless(
                \App\Models\Bus::where('id', $data['bus_id'])->where('school_id', $schoolId)->exists(),
                422,
                'Bus does not belong to your school.'
            );
        }

        if (! empty($data['bus_route_id'])) {
            abort_unless(
                \App\Models\BusRoute::where('id', $data['bus_route_id'])->where('school_id', $schoolId)->exists(),
                422,
                'Route does not belong to your school.'
            );
        }

        if (! empty($data['parent_id'])) {
            abort_unless(
                Student::where('parent_id', $data['parent_id'])->where('school_id', $schoolId)->exists()
                || \App\Models\ParentProfile::whereKey($data['parent_id'])->exists(),
                422,
                'Parent is not linked to your school.'
            );
        }
    }

    private function authorizeSchoolStudent(Request $request, Student $student): void
    {
        abort_unless(
            (int) $student->school_id === $this->schoolId($request),
            403,
            'You cannot access students from another school.'
        );
    }

    private function mapStudent(Student $s): \Illuminate\Support\Collection
    {
        return collect([
            'id' => $s->id,
            'name' => $s->full_name,
            'full_name' => $s->full_name,
            'grade' => $s->grade,
            'school_name' => $s->school_name,
            'school_id' => $s->school_id,
            'active' => $s->active,
            'status' => $s->active ? 'active' : 'inactive',
            'photo' => $s->photo ? asset('storage/' . $s->photo) : null,
            'qr_code' => $s->qr_code,
            'rfid_tag' => $s->rfid_tag,
            'bus_id' => $s->bus_id,
            'bus_route_id' => $s->bus_route_id,
            'bus' => $s->bus ? ['id' => $s->bus->id, 'bus_number' => $s->bus->bus_number] : null,
            'route' => $s->route ? ['id' => $s->route->id, 'name' => $s->route->name] : null,
            'parent_id' => $s->parent_id,
            'parent' => $s->parent ? [
                'id' => $s->parent->id,
                'phone' => $s->parent->phone,
                'user' => $s->parent->user ? [
                    'name' => $s->parent->user->name,
                    'email' => $s->parent->user->email,
                ] : null,
            ] : null,
            'created_at' => $s->created_at,
        ]);
    }
}
