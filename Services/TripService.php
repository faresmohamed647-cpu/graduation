<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Trip;
use App\Notifications\StudentTripEventNotification;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class TripService
{
    public function startTrip(Trip $trip): Trip
    {
        return DB::transaction(function () use ($trip) {
            if ($trip->status === 'active') {
                throw new \DomainException('Trip is already active.');
            }

            if ($trip->status === 'completed') {
                throw new \DomainException('Trip is already completed.');
            }

            $trip->update([
                'status' => 'active',
                'started_at' => $trip->started_at ?? CarbonImmutable::now(),
            ]);

            return $trip->refresh();
        });
    }

    public function endTrip(Trip $trip): Trip
    {
        return DB::transaction(function () use ($trip) {
            if ($trip->status === 'completed') {
                throw new \DomainException('Trip is already completed.');
            }

            if (! $trip->started_at && $trip->status !== 'active') {
                throw new \DomainException('Trip has not started yet.');
            }

            $trip->update([
                'status' => 'completed',
                'ended_at' => CarbonImmutable::now(),
            ]);

            return $trip->refresh();
        });
    }

    public function markPickedUp(Trip $trip, int $studentId): Attendance
    {
        return DB::transaction(function () use ($trip, $studentId) {
            if ($trip->status !== 'active') {
                throw new \DomainException('Trip must be active to pick up students.');
            }

            $attendance = Attendance::firstOrCreate(
                ['trip_id' => $trip->id, 'student_id' => $studentId],
                ['status' => 'absent']
            );

            if ($attendance->picked_up_at) {
                return $attendance;
            }

            $attendance->update([
                'picked_up_at' => CarbonImmutable::now(),
                'status' => 'picked_up',
            ]);

            $this->notifyParent($trip, $studentId, 'picked_up');

            return $attendance->refresh();
        });
    }

    public function markDroppedOff(Trip $trip, int $studentId): Attendance
    {
        return DB::transaction(function () use ($trip, $studentId) {
            if ($trip->status !== 'active') {
                throw new \DomainException('Trip must be active to drop off students.');
            }

            $attendance = Attendance::firstOrCreate(
                ['trip_id' => $trip->id, 'student_id' => $studentId],
                ['status' => 'absent']
            );

            if ($attendance->dropped_off_at) {
                return $attendance;
            }

            if (! $attendance->picked_up_at) {
                throw new \DomainException('Student must be picked up before drop-off.');
            }

            $attendance->update([
                'dropped_off_at' => CarbonImmutable::now(),
                'status' => 'dropped_off',
            ]);

            $this->notifyParent($trip, $studentId, 'dropped_off');

            return $attendance->refresh();
        });
    }

    private function notifyParent(Trip $trip, int $studentId, string $event): void
    {
        $student = Student::query()
            ->with('parent.user')
            ->find($studentId);

        $parentUser = $student?->parent?->user;

        if (! $parentUser) {
            return;
        }

        $parentUser->notify(new StudentTripEventNotification($trip, $studentId, $event));
    }
}

