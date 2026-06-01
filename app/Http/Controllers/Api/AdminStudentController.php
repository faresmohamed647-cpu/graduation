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
            'grade'       => $s->grade,
            'school_name' => $s->school_name,
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
            'grade'       => ['nullable', 'string', 'max:50'],
            'school_name' => ['nullable', 'string', 'max:255'],
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
            'grade'       => ['nullable', 'string', 'max:50'],
            'school_name' => ['nullable', 'string', 'max:255'],
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
