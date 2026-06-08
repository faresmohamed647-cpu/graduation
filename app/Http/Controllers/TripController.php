<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function __construct(private readonly TripService $tripService)
    {
    }

    public function start(Request $request, Trip $trip)
    {
        $this->authorizeTrip($request, $trip);

        try {
            $trip = $this->tripService->startTrip($trip);
        } catch (\DomainException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        return $this->okResponse($request, ['trip' => $trip]);
    }

    public function end(Request $request, Trip $trip)
    {
        $this->authorizeTrip($request, $trip);

        try {
            $trip = $this->tripService->endTrip($trip);
        } catch (\DomainException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        return $this->okResponse($request, ['trip' => $trip]);
    }

    public function pickup(Request $request, Trip $trip)
    {
        $this->authorizeTrip($request, $trip);

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
        ]);

        abort_unless(
            $trip->attendance()->where('student_id', $data['student_id'])->exists()
            || \App\Models\Student::whereKey($data['student_id'])->exists(),
            422,
            'Student is not assigned to this trip.'
        );

        try {
            $attendance = $this->tripService->markPickedUp($trip, (int) $data['student_id']);
        } catch (\DomainException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        return $this->okResponse($request, ['attendance' => $attendance]);
    }

    public function dropoff(Request $request, Trip $trip)
    {
        $this->authorizeTrip($request, $trip);

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
        ]);

        abort_unless(
            $trip->attendance()->where('student_id', $data['student_id'])->exists(),
            422,
            'Student is not assigned to this trip.'
        );

        try {
            $attendance = $this->tripService->markDroppedOff($trip, (int) $data['student_id']);
        } catch (\DomainException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        return $this->okResponse($request, ['attendance' => $attendance]);
    }

    private function authorizeTrip(Request $request, Trip $trip): void
    {
        // Lightweight ownership check without Policies for now
        $driverId = $request->user()?->driverProfile?->id;
        if (! $driverId || $trip->driver_id !== $driverId) {
            abort(403);
        }
    }

    private function okResponse(Request $request, array $payload)
    {
        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back();
    }

    private function errorResponse(Request $request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withErrors(['trip' => $message]);
    }
}

