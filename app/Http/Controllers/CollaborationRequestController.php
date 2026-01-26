<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CollaborationRequest;
use App\Models\Campaign;
use App\Models\Notification;
use App\Models\NotificationCollaborationRequest;

class CollaborationRequestController extends Controller
{
    /**
     * Store a new collaboration request and send notification to campaign creator.
     */
    public function store(Request $request, Campaign $campaign)
    {
        $user = Auth::user();

        // Validate request
        $validated = $request->validate([
            'message' => 'nullable|string|max:200',
        ]);

        // Check if user is trying to request collaboration on their own campaign
        if ($campaign->creator_id === $user->user_id) {
            return back()->with('error', 'You cannot request to collaborate on your own campaign.');
        }

        // Check if campaign is active
        if ($campaign->status !== 'active') {
            return back()->with('error', 'You cannot request to collaborate on a ' . $campaign->status . ' campaign.');
        }

        // Check if campaign already has 5 collaborators
        if ($campaign->collaborators()->count() >= 5) {
            return back()->with('error', 'This campaign already has the maximum number of collaborators (5).');
        }

        // Check if user is already a collaborator
        if ($campaign->collaborators()->where('campaign_collaborators.user_id', $user->user_id)->exists()) {
            return back()->with('error', 'You are already a collaborator on this campaign.');
        }

        // Check if user already has a pending request
        $existingRequest = CollaborationRequest::where('campaign_id', $campaign->campaign_id)
            ->where('requester_id', $user->user_id)
            ->first();

        if ($existingRequest) {
            if ($existingRequest->isPending()) {
                return back()->with('error', 'You already have a pending request for this campaign.');
            }
            return back()->with('error', 'You have already made a request for this campaign.');
        }

        DB::transaction(function() use ($campaign, $user, $validated) {
            // Create the collaboration request
            $collabRequest = CollaborationRequest::create([
                'campaign_id' => $campaign->campaign_id,
                'requester_id' => $user->user_id,
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
            ]);

            // Create notification for campaign creator
            $notification = Notification::create([
                'content' => $user->name . ' wants to collaborate with you on "' . $campaign->title . '"',
                'user_id' => $campaign->creator_id,
                'is_read' => false,
            ]);

            // Link notification to collaboration request
            NotificationCollaborationRequest::create([
                'notification_id' => $notification->notification_id,
                'request_id' => $collabRequest->request_id,
            ]);
        });

        return back()->with('success', 'Collaboration request sent successfully!');
    }

    /**
     * Accept a collaboration request.
     */
    public function accept(CollaborationRequest $request)
    {
        $user = Auth::user();
        $campaign = $request->campaign;

        // Check if user is the campaign creator
        if ($campaign->creator_id !== $user->user_id) {
            return back()->with('error', 'Only the campaign creator can accept collaboration requests.');
        }

        // Check if request is still pending
        if (!$request->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        // Check if campaign already has 5 collaborators
        if ($campaign->collaborators()->count() >= 5) {
            return back()->with('error', 'This campaign already has the maximum number of collaborators (5).');
        }

        // Check if requester is already a collaborator
        if ($campaign->collaborators()->where('campaign_collaborators.user_id', $request->requester_id)->exists()) {
            return back()->with('error', 'This user is already a collaborator on this campaign.');
        }

        DB::transaction(function() use ($request, $campaign) {
            // Add the user as a collaborator
            $campaign->collaborators()->attach($request->requester_id, [
                'added_at' => now()
            ]);

            // Update request status
            $request->update([
                'status' => 'accepted'
            ]);

            // Create notification for the requester
            $notification = Notification::create([
                'content' => 'Your collaboration request for "' . $campaign->title . '" has been accepted!',
                'user_id' => $request->requester_id,
                'is_read' => false,
            ]);

            // Link notification to collaboration request
            NotificationCollaborationRequest::create([
                'notification_id' => $notification->notification_id,
                'request_id' => $request->request_id,
            ]);

            // If campaign now has 5 collaborators, reject all other pending requests
            if ($campaign->collaborators()->count() >= 5) {
                CollaborationRequest::where('campaign_id', $campaign->campaign_id)
                    ->where('request_id', '!=', $request->request_id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
            }
        });

        return back()->with('success', 'Collaboration request accepted! The user is now a collaborator on your campaign.');
    }

    /**
     * Reject a collaboration request.
     */
    public function reject(CollaborationRequest $request)
    {
        $user = Auth::user();
        $campaign = $request->campaign;

        // Check if user is the campaign creator
        if ($campaign->creator_id !== $user->user_id) {
            return back()->with('error', 'Only the campaign creator can reject collaboration requests.');
        }

        // Check if request is still pending
        if (!$request->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        // Update request status
        $request->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'Collaboration request rejected.');
    }

    /**
     * Cancel a collaboration request (by the requester).
     */
    public function cancel(CollaborationRequest $request)
    {
        $user = Auth::user();

        // Check if user is the requester
        if ($request->requester_id !== $user->user_id) {
            return back()->with('error', 'You can only cancel your own requests.');
        }

        // Check if request is still pending
        if (!$request->isPending()) {
            return back()->with('error', 'You can only cancel pending requests.');
        }

        // Delete the request
        $request->delete();

        return back()->with('success', 'Collaboration request cancelled.');
    }

    /**
     * Remove a collaborator from a campaign.
     */
    public function removeCollaborator(Campaign $campaign, Request $request)
    {
        $user = Auth::user();

        // Check if user is the campaign creator
        if ($campaign->creator_id !== $user->user_id) {
            return back()->with('error', 'Only the campaign creator can remove collaborators.');
        }

        $validated = $request->validate([
            'collaborator_id' => 'required|integer|exists:user,user_id'
        ]);

        // Check if the user is actually a collaborator
        if (!$campaign->collaborators()->where('campaign_collaborators.user_id', $validated['collaborator_id'])->exists()) {
            return back()->with('error', 'This user is not a collaborator on this campaign.');
        }

        // Remove the collaborator
        $campaign->collaborators()->detach($validated['collaborator_id']);

        return back()->with('success', 'Collaborator removed successfully.');
    }
}
