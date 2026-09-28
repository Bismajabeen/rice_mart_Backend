<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class NotificationService
{
    private const ADMIN_TYPE_PERMISSIONS = [
        'payment_pending' => 'view all payments',
        'payment_release' => 'manage payments',
        'shop_pending'    => 'view all shops',
        'review'          => 'view all shops',
        'complaint'       => 'view complaints',
    ];

    private const ADMIN_FALLBACK_PERMISSION = 'view admin dashboard';

    public static function send(?User $user, string $type, string $title, string $body = '', array $data = []): ?AppNotification
    {
        
        if (!$user) {
            return null;
        }

        return AppNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public static function sendToRoles(array $roles, string $type, string $title, string $body = '', array $data = []): void
    {
        $users = User::role($roles)->get();

        foreach ($users as $user) {
            self::send($user, $type, $title, $body, $data);
        }
    }

    public static function sendToAdmins(string $type, string $title, string $body = '', array $data = []): void
    {
        $permission = self::ADMIN_TYPE_PERMISSIONS[$type]
            ?? self::ADMIN_FALLBACK_PERMISSION;

        // Super Admin ALWAYS receives every admin notification.
        $recipients = User::role('super_admin')->get();

        try {
            // Admins receive it only if they currently hold the matching permission.
            $admins = User::role('admin')->permission($permission)->get();
            $recipients = $recipients->merge($admins)->unique('id');
        } catch (PermissionDoesNotExist $e) {
            // Super Admin still gets it, and a notification problem
            // never breaks an order or payment flow.
            report($e);
        }

        foreach ($recipients as $user) {
            self::send($user, $type, $title, $body, $data);
        }
    }
}
