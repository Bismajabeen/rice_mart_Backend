<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // =========================
    // LIST (paginated, latest first) — this is what feeds the bell dropdown/list
    // =========================
    public function index(Request $request)
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->whereNull('cleared_at')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    // =========================
    // UNREAD COUNT — powers the little red badge on the bell icon
    // =========================
    public function unreadCount(Request $request)
    {
        $count = AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    // =========================
    // MARK ONE AS READ (called when the user taps a notification)
    // =========================
    public function markAsRead(Request $request, $id)
    {
        $notification = AppNotification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'notification' => $notification,
        ]);
    }

    // =========================
    // MARK ALL AS READ (the "mark as read" action at the top of the bell list)
    // =========================
    public function markAllAsRead(Request $request)
    {
        AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    // =========================
    // CLEAR ALL — hides every notification for the current user.
    // Rows stay in the database; cleared_at is filled in.
    // is_read is set to true so the bell badge goes to 0.
    // =========================
    public function clearAll(Request $request)
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('cleared_at')
            ->update([
                'cleared_at' => now(),
                'is_read' => true,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared',
        ]);
    }
}