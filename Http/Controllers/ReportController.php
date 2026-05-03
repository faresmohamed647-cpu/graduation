<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Report::query()->latest();

        if (($user->role ?? null) !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->limit(100)->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'trip_id' => ['nullable', 'integer'],
        ]);

        Report::create([
            'user_id' => $request->user()->id,
            'trip_id' => $data['trip_id'] ?? null,
            'type' => $data['type'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'status' => 'open',
        ]);

        return back();
    }
}

