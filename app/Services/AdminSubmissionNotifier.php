<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminSubmissionNotification;

class AdminSubmissionNotifier
{
    public static function notify(
        string $submissionType,
        string $title,
        string $body,
        array $meta = [],
    ): void {
        $admins = User::query()->where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return;
        }

        $notification = new AdminSubmissionNotification($submissionType, $title, $body, $meta);

        foreach ($admins as $admin) {
            $admin->notify($notification);
        }
    }
}
