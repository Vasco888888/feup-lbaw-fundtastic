<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationPolicy
{
    /**
     * Determine whether the user can view the notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return $user->user_id === $notification->user_id;
    }

    /**
     * Determine whether the user can update the notification (mark as read).
     */
    public function update(User $user, Notification $notification): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return $user->user_id === $notification->user_id;
    }
}
