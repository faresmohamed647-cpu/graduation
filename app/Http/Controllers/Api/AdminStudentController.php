<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['parent.user'])->latest('id');

        if ($search = $request->get('search')) {
            $query->where('full_name', 'like', "%{$search}%");
        }

        $perPage = $request->get('per_page', 25);
        $students = $perPage === 'all' ? $query->get() : $query->paginate((int) $perPage);

        $items = $perPage === 'all' ? $students : $students->items();

        $mapped = collect($items)->map(fn (Student $s) => [
            'id'          => $s->id,
            'name'        => $s->full_name,
            'age'         => $s->age,
            'grade'       => $s->grade,
            'school_name' => $s->school_name,
            'pickup_location' => $s->pickup_location,
            'dropoff_location' => $s->dropoff_location,
            'pickup_time' => $s->pickup_time,
            'dropoff_time' => $s->dropoff_time,
            'has_medical_condition' => $s->has_medical_condition,
            'medical_condition' => $s->medical_condition,
            'medication' => $s->medication,
            'assignment_status' => $s->assignment_status,
            'active'      => $s->active,
            'parent_id'   => $s->parent_id,
            'parent'      => $s->parent ? [
                'id'   => $s->parent->id,
                'user' => $s->parent->user ? ['name' => $s->parent->user->name, 'email' => $s->parent->user->email] : null,
            ] : null,
            'created_at'  => $s->created_at,
        ]);

        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function show(Student $student)
    {
        $student->load('parent.user');
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'   => ['required', 'string', 'max:255'],
            'parent_id'   => ['required', 'integer', 'exists:parents,id'],
            'age'         => ['nullable', 'integer', 'min:2', 'max:25'],
            'grade'       => ['nullable', 'string', 'max:50'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'dropoff_location' => ['nullable', 'string', 'max:255'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'dropoff_time' => ['nullable', 'date_format:H:i'],
            'has_medical_condition' => ['sometimes', 'boolean'],
            'medical_condition' => ['nullable', 'string', 'max:2000'],
            'medication' => ['nullable', 'string', 'max:2000'],
            'assignment_status' => ['nullable', 'string', 'in:pending,assigned'],
            'active'      => ['sometimes', 'boolean'],
        ]);

        $student = Student::create($data);

        return response()->json(['success' => true, 'data' => $student, 'message' => 'Student created'], 201);
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'full_name'   => ['sometimes', 'string', 'max:255'],
            'parent_id'   => ['sometimes', 'integer', 'exists:parents,id'],
            'age'         => ['nullable', 'integer', 'min:2', 'max:25'],
            'grade'       => ['nullable', 'string', 'max:50'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'dropoff_location' => ['nullable', 'string', 'max:255'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'dropoff_time' => ['nullable', 'date_format:H:i'],
            'has_medical_condition' => ['sometimes', 'boolean'],
            'medical_condition' => ['nullable', 'string', 'max:2000'],
            'medication' => ['nullable', 'string', 'max:2000'],
            'assignment_status' => ['nullable', 'string', 'in:pending,assigned'],
            'active'      => ['sometimes', 'boolean'],
        ]);

        $student->update($data);

        return response()->json(['success' => true, 'data' => $student, 'message' => 'Student updated']);
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json(['success' => true, 'message' => 'Student deleted']);
    }
}
