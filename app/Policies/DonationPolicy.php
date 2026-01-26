<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;

class DonationPolicy
{
    /**
     * Determine whether the user can create donations.
     */
    public function create(User $user): bool
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can view a donation.
     * Donations are generally public for display purposes.
     */
    public function view(?User $user, Donation $donation): bool
    {
        return true;
    }

}
