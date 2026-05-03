<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->limit(50)->get();
        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ]);
        $target = User::findOrFail($data['user_id']);
        $target->notify(new class($data['title'] ?? '', $data['body'] ?? '') extends Notification {
            use \Illuminate\Bus\Queueable;
            public function __construct(private readonly string $title, private readonly string $body) {}
            public function via($n): array { return ['database']; }
            public function toArray($n): array { return ['title' => $this->title, 'body' => $this->body]; }
        });
        return response()->json(['success' => true, 'message' => 'Notification sent']);
    }

    public function sendBulk(Request $request)
    {
        $data = $request->validate([
            'role' => ['sometimes', 'string', 'in:admin,driver,parent'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ]);
        $query = User::query();
        if (isset($data['role'])) { $query->where('role', $data['role']); }
        $users = $query->get();
        foreach ($users as $user) {
            $user->notify(new class($data['title'] ?? '', $data['body'] ?? '') extends Notification {
                use \Illuminate\Bus\Queueable;
                public function __construct(private readonly string $title, private readonly string $body) {}
                public function via($n): array { return ['database']; }
                public function toArray($n): array { return ['title' => $this->title, 'body' => $this->body]; }
            });
        }
        return response()->json(['success' => true, 'message' => "Sent to {$users->count()} users"]);
    }
}
