<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Determine whether the user can view the given profile.
     * Profiles are public.
     */
    public function view(?User $user, User $profile): bool
    {
        return true;
    }

    /**
     * Determine whether the authenticated user can update the profile.
     * Only the owner may update their profile.
     */
    public function update(User $user, User $profile): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $user->user_id === $profile->user_id;
    }

    /**
     * Determine whether the authenticated user can delete their own account.
     * Only the owner may delete their own account.
     */
    public function delete(User $user, User $profile): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $user->user_id === $profile->user_id;
    }
}
