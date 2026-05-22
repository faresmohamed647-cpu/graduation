<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminSubmissionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $submissionType,
        public readonly string $title,
        public readonly string $body,
        public readonly array $meta = [],
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_submission',
            'submission_type' => $this->submissionType,
            'title' => $this->title,
            'body' => $this->body,
            'meta' => $this->meta,
            'action' => $this->meta['action'] ?? 'requests',
        ];
    }
}
