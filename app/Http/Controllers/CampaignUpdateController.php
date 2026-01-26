<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignUpdate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CampaignUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Creates a new update for the given campaign.
     * API endpoint: POST /api/campaigns/{id}/updates (R306)
     */
    public function store(Request $request, Campaign $campaign)
    {
        // Prepare a new CampaignUpdate instance already tied to the given Campaign.
        // Using make() builds the object in memory without saving it yet.
        $update = $campaign->updates()->make([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author_id' => Auth::id(),
            'date' => now(),
        ]);

        // Authorize using the CampaignPolicy (postUpdate)
        Gate::authorize('postUpdate', $campaign);

        // Validate the request data.
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Persist the update.
        $update->save();

        // Load author relationship for response.
        $update->load('author');

        // Return the update as JSON for the frontend.
        return response()->json([
            'update_id' => $update->update_id,
            'title' => $update->title,
            'content' => $update->content,
            'date' => $update->date->toISOString(),
            'author' => [
                'user_id' => $update->author->user_id,
                'name' => $update->author->name,
            ]
        ], 201);
    }
}
