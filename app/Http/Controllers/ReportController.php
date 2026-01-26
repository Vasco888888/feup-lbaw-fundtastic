<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Models\Campaign;
use App\Models\Comment;

class ReportController extends Controller
{
    /** Store a report submitted by an authenticated user. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'target_type' => 'required|string',
            'target_id' => 'required|integer',
            'reason' => 'required|string|max:2000',
        ]);

        // Defensive: admins should not be able to create reports.
        if (Auth::guard('admin')->check() || session('is_admin')) {
            return response()->json(['ok' => false, 'error' => 'Administrators cannot create reports.'], 403);
        }

        // Map incoming UI fields to the existing `report` table.
        // The project's DB stores reports using `comment_id` (not polymorphic fields).
        $reportPayload = [
            'user_id' => Auth::id(),
            'reason' => $data['reason'],
            'date' => now(),
        ];

        // Populate either `comment_id` or `campaign_id` depending on type.
        if (isset($data['target_type'])) {
            if ($data['target_type'] === 'comment') {
                $comment = Comment::find($data['target_id']);
                if (!$comment) {
                    return response()->json(['ok' => false, 'error' => 'Comment not found.'], 404);
                }
                // Owners cannot report their own comments.
                if ($comment->user_id === Auth::id()) {
                    return response()->json(['ok' => false, 'error' => 'You cannot report your own comment.'], 403);
                }

                $reportPayload['comment_id'] = $data['target_id'];
            } elseif ($data['target_type'] === 'campaign') {
                $campaign = Campaign::find($data['target_id']);
                if (!$campaign) {
                    return response()->json(['ok' => false, 'error' => 'Campaign not found.'], 404);
                }
                // Owners cannot report their own campaigns.
                if ($campaign->creator_id === Auth::id()) {
                    return response()->json(['ok' => false, 'error' => 'You cannot report your own campaign.'], 403);
                }

                $reportPayload['campaign_id'] = $data['target_id'];
            }
        }

        $report = Report::create($reportPayload);

        // Return JSON for AJAX clients.
        return response()->json(['ok' => true, 'report_id' => $report->report_id]);
    }
}
