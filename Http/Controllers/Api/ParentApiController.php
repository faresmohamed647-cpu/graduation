<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ParentApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if (!$parentId) {
            return response()->json(['success' => true, 'data' => ['children' => [], 'latest_trip' => null]]);
        }
        $children = Student::where('parent_id', $parentId)->where('active', true)->get()
            ->map(fn ($s) => ['id' => $s->id, 'full_name' => $s->full_name, 'grade' => $s->grade, 'school_name' => $s->school_name]);
        $latestTrip = Trip::whereHas('attendance', fn ($q) => $q->whereIn('student_id', $children->pluck('id')))
            ->latest('id')->first();
        return response()->json(['success' => true, 'data' => ['children' => $children, 'latest_trip' => $latestTrip]]);
    }

    public function children(Request $request)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if (!$parentId) { return response()->json(['success' => true, 'data' => []]); }
        $students = Student::where('parent_id', $parentId)->get();
        return response()->json(['success' => true, 'data' => $students]);
    }

    public function child(Request $request, Student $student)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if ($student->parent_id !== $parentId) { abort(403); }
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function childAttendance(Request $request, Student $student)
    {
        $parentId = $request->user()?->parentProfile?->id;
        if ($student->parent_id !== $parentId) { abort(403); }
        $attendance = $student->attendance()->with('trip')->latest()->limit(30)->get();
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    public function notifications(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()->notifications()->latest()->limit(30)->get()]);
    }

    public function markNotificationRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->whereKey($id)->firstOrFail();
        $n->markAsRead();
        return response()->json(['success' => true, 'message' => 'Marked as read']);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All marked as read']);
    }
}
