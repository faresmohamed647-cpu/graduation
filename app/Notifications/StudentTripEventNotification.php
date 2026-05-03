<?php

namespace App\Notifications;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentTripEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Trip $trip,
        public readonly int $studentId,
        public readonly string $event, // picked_up|dropped_off
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event' => $this->event,
            'trip_id' => $this->trip->id,
            'student_id' => $this->studentId,
            'trip_date' => optional($this->trip->trip_date)->toDateString(),
            'shift' => $this->trip->shift,
            'status' => $this->trip->status,
        ];
    }
}

