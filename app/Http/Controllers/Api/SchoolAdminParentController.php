<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\Student;
use Illuminate\Http\Request;

class SchoolAdminParentController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);

        $parentIds = Student::where('school_id', $schoolId)
            ->pluck('parent_id')
            ->unique()
            ->filter();

        $parents = ParentProfile::with(['user', 'students' => fn ($q) => $q->where('school_id', $schoolId)])
            ->whereIn('id', $parentIds)
            ->latest('id')
            ->get()
            ->map(fn (ParentProfile $parent) => [
                'id' => $parent->id,
                'name' => $parent->user?->name,
                'email' => $parent->user?->email,
                'phone' => $parent->phone,
                'address' => $parent->address,
                'active' => $parent->active,
                'children' => $parent->students->map(fn (Student $s) => [
                    'id' => $s->id,
                    'name' => $s->full_name,
                    'grade' => $s->grade,
                    'active' => $s->active,
                ])->values(),
                'children_count' => $parent->students->count(),
            ]);

        return response()->json(['success' => true, 'data' => $parents]);
    }

    public function show(Request $request, ParentProfile $parent)
    {
        $schoolId = $this->schoolId($request);
        $hasChild = Student::where('school_id', $schoolId)->where('parent_id', $parent->id)->exists();
        abort_unless($hasChild, 403);

        $parent->load(['user', 'students' => fn ($q) => $q->where('school_id', $schoolId)->with(['bus', 'route'])]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $parent->id,
                'name' => $parent->user?->name,
                'email' => $parent->user?->email,
                'phone' => $parent->phone,
                'address' => $parent->address,
                'active' => $parent->active,
                'students' => $parent->students,
            ],
        ]);
    }
}
