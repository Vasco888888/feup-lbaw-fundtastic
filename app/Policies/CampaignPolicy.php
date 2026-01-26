<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class CampaignPolicy
{
    /**
     * Helper method to check if user is the creator or a collaborator.
     */
    private function isOwnerOrCollaborator(User $user, Campaign $campaign): bool
    {
        return $user->user_id === $campaign->creator_id 
            || $campaign->collaborators()->where('campaign_collaborators.user_id', $user->user_id)->exists();
    }

    /**
     * Determine whether the user can create campaigns.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a campaign.
        return Auth::check();
    }

    /**
     * Determine whether the user can update the campaign.
     * The creator or any collaborator may update, but only if there are no donations yet.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Check if user is creator or collaborator
        if (!$this->isOwnerOrCollaborator($user, $campaign)) {
            return false;
        }

        // Prevent editing once any donations exist
        return $campaign->donations()->count() === 0;
    }

    /**
     * Determine whether the user can post updates to the campaign.
     * The campaign creator or any collaborator may post updates even after donations exist.
     */
    public function postUpdate(User $user, Campaign $campaign): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->isOwnerOrCollaborator($user, $campaign);
    }

    /**
     * Determine whether the user can upload media to the campaign.
     * Allow the campaign creator or any collaborator to upload media even after donations exist.
     */
    public function uploadMedia(User $user, Campaign $campaign): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->isOwnerOrCollaborator($user, $campaign);
    }

    /**
     * Determine whether the user can delete media from the campaign.
     * Allow the campaign creator or any collaborator to delete media.
     */
    public function deleteMedia(User $user, Campaign $campaign): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->isOwnerOrCollaborator($user, $campaign);
    }

    /**
     * Determine whether the user can delete the campaign.
     * Only the creator can delete, and only if no donations exist.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Only the creator can delete
        if ($user->user_id !== $campaign->creator_id) {
            return false;
        }

        // Prevent deletion once any donations exist
        return $campaign->donations()->count() === 0;
    }
}
