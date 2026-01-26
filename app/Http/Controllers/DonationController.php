<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Events\DonationReceived;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Creates a new donation for the given campaign.
     * API endpoint: POST /api/campaigns/{id}/donate (R301)
     */
    public function store(Request $request, Campaign $campaign)
    {
        // Admins are not allowed to donate.
        if (Auth::guard('admin')->check()) {
            return redirect()->route('campaigns.show', $campaign->campaign_id)
                ->with('error', 'Administrators are not allowed to make donations.');
        }
        // Validate the request data.
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'boolean',
        ]);

        // Prevent donating to a campaign that is not active (completed, suspended, deleted)
        if ($campaign->status !== 'active') {
            return redirect()->route('campaigns.show', $campaign->campaign_id)
                ->with('error', 'This campaign is not accepting donations at the moment.');
        }

        // Use a transaction to ensure data consistency.
        $donation = DB::transaction(function () use ($validated, $campaign) {
            // Prepare a new Donation instance already tied to the given Campaign.
            $donation = new Donation();
            $donation->amount = $validated['amount'];
            $donation->message = $validated['message'] ?? null;
            $donation->is_anonymous = $validated['is_anonymous'] ?? false;
            $donation->donator_id = Auth::id();
            $donation->campaign_id = $campaign->campaign_id;
            $donation->date = now();

            // Persist the donation. The DB trigger will recompute the campaign's
            // `current_amount` and update `status` if the goal is reached.
            $donation->save();

            return $donation;
        });

        // Dispatch notification event
        event(new DonationReceived($donation));

        // Redirect back to campaign page with success message
        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Thank you for your donation of €' . number_format($donation->amount, 2) . '!');
    }
}
