<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Models\Notification;
use App\Models\NotificationComment;

class NotifyCommentPosted
{
    public function handle(CommentPosted $event): void
    {
        $comment = $event->comment;
        $campaign = $comment->campaign;
        
        // Don't notify if no campaign or if campaign creator is the commenter
        if (!$campaign || $comment->user_id == $campaign->creator_id) {
            return;
        }

        // Check if notification already exists for this comment to avoid duplicates
        $existing = NotificationComment::where('comment_id', $comment->comment_id)->first();
        if ($existing) {
            return;
        }

        $commenterName = $comment->user->name ?? 'Someone';
        $content = "{$commenterName} commented on your campaign '{$campaign->title}'";

        // Create notification
        $notification = Notification::create([
            'content' => $content,
            'user_id' => $campaign->creator_id,
            'created_at' => now(),
            'is_read' => false,
        ]);

        // Link to comment
        NotificationComment::create([
            'notification_id' => $notification->notification_id,
            'comment_id' => $comment->comment_id,
        ]);
    }
}
