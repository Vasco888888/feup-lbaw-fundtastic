<?php

namespace App\Http\Controllers;

use App\Models\Campaign;

use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Follow a campaign.
     * API endpoint: POST /api/campaigns/{id}/follow (R302)
     */
    public function store(Campaign $campaign)
    {
        $user = Auth::user();

        // Check if already following.
        if (!$user->followedCampaigns()->where('lbaw2532.user_follows_campaign.campaign_id', $campaign->campaign_id)->exists()) {
            // Attach the campaign to the user's followed campaigns.
            $user->followedCampaigns()->attach($campaign->campaign_id);
        }

        // Redirect back to campaign page
        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'You are now following this campaign!');
    }

    /**
     * Unfollow a campaign.
     * API endpoint: DELETE /api/campaigns/{id}/unfollow (R303)
     */
    public function destroy(Campaign $campaign)
    {
        $user = Auth::user();

        // Detach the campaign from the user's followed campaigns.
        $user->followedCampaigns()->detach($campaign->campaign_id);

        // Redirect back to campaign page
        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'You unfollowed this campaign.');
    }
}
