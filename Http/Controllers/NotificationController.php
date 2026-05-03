<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\StudentTripEventNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->limit(50)->get();

        return response()->json($notifications);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'unread' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ]);

        $target = User::query()->findOrFail($data['user_id']);

        // Generic database notification shape using a small wrapper notification
        $target->notify(new class($data['title'] ?? null, $data['body'] ?? null) extends \Illuminate\Notifications\Notification {
            use \Illuminate\Bus\Queueable;

            public function __construct(private readonly ?string $title, private readonly ?string $body)
            {
            }

            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toArray(object $notifiable): array
            {
                return [
                    'title' => $this->title,
                    'body' => $this->body,
                ];
            }
        });

        return response()->json(['ok' => true]);
    }

    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }
}

