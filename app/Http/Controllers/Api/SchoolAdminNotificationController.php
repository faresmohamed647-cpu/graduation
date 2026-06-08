<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Notifications\SchoolAnnouncementNotification;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SchoolAdminNotificationController extends Controller
{
    use ResolvesSchoolScope;

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->limit(50)->get();

        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function send(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'type' => ['sometimes', 'string', 'in:general,emergency,announcement,delay'],
        ]);

        $target = User::findOrFail($data['user_id']);
        $this->ensureParentOfSchool($schoolId, $target);

        $target->notify(new SchoolAnnouncementNotification(
            $data['title'],
            $data['body'],
            $data['type'] ?? 'general',
        ));

        $logger->log($request, 'notification.sent', null, ['user_id' => $target->id]);

        return response()->json(['success' => true, 'message' => 'Notification sent']);
    }

    public function center(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $parentUserIds = Student::where('school_id', $schoolId)
            ->with('parent.user')
            ->get()
            ->pluck('parent.user.id')
            ->filter()
            ->unique();

        $sent = \Illuminate\Support\Facades\DB::table('notifications')
            ->whereIn('notifiable_id', $parentUserIds)
            ->where('notifiable_type', \App\Models\User::class)
            ->count();

        $read = \Illuminate\Support\Facades\DB::table('notifications')
            ->whereIn('notifiable_id', $parentUserIds)
            ->where('notifiable_type', \App\Models\User::class)
            ->whereNotNull('read_at')
            ->count();

        $mine = $request->user()->notifications()->latest()->limit(30)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sent_total' => $sent,
                'read_total' => $read,
                'unread_total' => max(0, $sent - $read),
                'failed_total' => 0,
                'recent' => $mine,
            ],
        ]);
    }

    public function sendBulk(Request $request, ActivityLogService $logger)
    {
        $schoolId = $this->schoolId($request);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'type' => ['sometimes', 'string', 'in:general,emergency,announcement,delay'],
            'channel' => ['sometimes', 'string', 'in:in-app,email,sms'],
        ]);

        $parentUserIds = Student::where('school_id', $schoolId)
            ->with('parent.user')
            ->get()
            ->pluck('parent.user.id')
            ->filter()
            ->unique();

        $users = User::whereIn('id', $parentUserIds)->get();
        $notification = new SchoolAnnouncementNotification(
            $data['title'],
            $data['body'],
            $data['type'] ?? 'announcement',
            $data['channel'] ?? 'in-app',
        );

        foreach ($users as $user) {
            $user->notify($notification);
        }

        $logger->log($request, 'notification.broadcast', null, ['recipients' => $users->count()]);

        return response()->json(['success' => true, 'message' => "Sent to {$users->count()} parents"]);
    }

    private function ensureParentOfSchool(int $schoolId, User $user): void
    {
        $hasChild = Student::where('school_id', $schoolId)
            ->whereHas('parent', fn ($q) => $q->where('user_id', $user->id))
            ->exists();

        abort_unless($hasChild, 403, 'You can only notify parents of your school students.');
    }
}
