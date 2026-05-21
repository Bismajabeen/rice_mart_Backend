<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;

class NotificationController extends Controller
{
    // =========================
    // CREATE NOTIFICATION
    // =========================
    public static function createNotification(
        $userId,
        $title,
        $message,
        $type
    ) {

        UserNotification::create([

            'user_id' => $userId,

            'title' => $title,

            'message' => $message,

            'type' => $type,

            'is_read' => 0,
        ]);
    }

    // =========================
    // MY NOTIFICATIONS
    // =========================
    public function myNotifications(Request $request)
    {
        $notifications = UserNotification::where(
            'user_id',
            $request->user()->id
        )
        ->latest()
        ->get();

        return response()->json([

            'success' => true,

            'notifications' => $notifications
        ]);
    }

    // =========================
    // MARK AS READ
    // =========================
    public function markAsRead($id)
    {
        $notification = UserNotification::find($id);

        if (!$notification) {

            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->update([
            'is_read' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
}