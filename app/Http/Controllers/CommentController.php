<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Comment;
use App\Events\CommentPosted;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment for a campaign.
     * POST /api/campaigns/{campaign}/comments
     */
    public function store(Request $request, Campaign $campaign)
    {
        // Admins are not allowed to comment.
        if (Auth::guard('admin')->check()) {
            return redirect()->route('campaigns.show', $campaign->campaign_id)
                ->with('error', 'Administrators are not allowed to post comments.');
        }
        $validated = $request->validate([
            'text' => 'required|string|max:2000',
        ]);

        $comment = new Comment();
        $comment->text = $validated['text'];
        $comment->date = now();
        $comment->user_id = Auth::id();
        $comment->campaign_id = $campaign->campaign_id;
        $comment->save();

        // Dispatch notification event
        event(new CommentPosted($comment));

        return redirect()->route('campaigns.show', $campaign->campaign_id)
            ->with('success', 'Comment posted successfully.');
    }

    /**
     * Delete a user's own comment.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Only the comment owner can delete via this route.
        if (auth()->guard('admin')->check() || auth()->id() !== $comment->user_id) {
            abort(403);
        }

        $campaignId = $comment->campaign_id;
        $comment->delete();

        if ($campaignId) {
            return redirect()->route('campaigns.show', $campaignId)->with('success', 'Comment deleted.');
        }

        return redirect()->back()->with('success', 'Comment deleted.');
    }
}
