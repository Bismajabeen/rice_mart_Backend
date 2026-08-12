<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    // =========================
    // SEND TO ONE USER
    // =========================
    public static function send(?User $user, string $type, string $title, string $body = '', array $data = []): ?AppNotification
    {
        // Defensive: some relations (shop->user, etc.) can be null in edge cases.
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

    // =========================
    // SEND TO EVERY USER HOLDING ANY OF THE GIVEN ROLES
    // (uses Spatie's HasRoles::role() scope, already on your User model)
    // =========================
    public static function sendToRoles(array $roles, string $type, string $title, string $body = '', array $data = []): void
    {
        $users = User::role($roles)->get();

        foreach ($users as $user) {
            self::send($user, $type, $title, $body, $data);
        }
    }

    // =========================
    // CONVENIENCE: NOTIFY ADMINS
    // =========================
    public static function sendToAdmins(string $type, string $title, string $body = '', array $data = []): void
    {
        self::sendToRoles(['admin', 'super_admin'], $type, $title, $body, $data);
    }
}
