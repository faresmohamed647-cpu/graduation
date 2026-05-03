<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function dashboard(Request $request)
    {
        $parentId = $request->user()?->parentProfile?->id;

        $students = [];
        if ($parentId) {
            $students = Student::query()
                ->where('parent_id', $parentId)
                ->where('active', true)
                ->orderBy('full_name')
                ->get();
        }

        return view('parent.parent', compact('students'));
    }

    public function report()
    {
        return view('parent.report');
    }

    public function dashboardData(Request $request)
    {
        $user = $request->user();
        $parentId = $user?->parentProfile?->id;

        if (! $parentId) {
            return response()->json([
                'children' => [],
                'latest_trip' => null,
                'notifications' => [],
                'message' => 'Parent profile missing.',
            ], 200);
        }

        $children = Student::query()
            ->where('parent_id', $parentId)
            ->where('active', true)
            ->orderBy('full_name')
            ->get()
            ->map(fn (Student $s) => [
                'id' => $s->id,
                'full_name' => $s->full_name,
                'grade' => $s->grade,
                'school_name' => $s->school_name,
            ])
            ->values();

        // Latest trip for any of the parent's children (based on attendance join)
        $latestTrip = Trip::query()
            ->whereDate('trip_date', '<=', CarbonImmutable::today())
            ->whereHas('attendance', function ($q) use ($children) {
                $q->whereIn('student_id', $children->pluck('id')->all());
            })
            ->latest('id')
            ->first();

        return response()->json([
            'children' => $children,
            'latest_trip' => $latestTrip,
            'notifications' => $user->notifications()->latest()->limit(20)->get(),
        ]);
    }
}

