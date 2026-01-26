<?php

namespace App\Listeners;

use App\Events\CampaignUpdatePublished;
use App\Models\Notification;
use App\Models\NotificationCampaignUpdate;

class NotifyCampaignUpdatePublished
{
    public function handle(CampaignUpdatePublished $event): void
    {
        $update = $event->update;
        $campaign = $update->campaign;
        
        if (!$campaign) {
            return;
        }

        // Check if notifications already exist for this update to avoid duplicates
        $existing = NotificationCampaignUpdate::where('update_id', $update->update_id)->exists();
        if ($existing) {
            return;
        }

        // Get all donors (unique user IDs)
        $donorIds = $campaign->donations()
            ->whereNotNull('donator_id')
            ->pluck('donator_id')
            ->unique();

        // Get all followers
        $followerIds = $campaign->followers()->pluck('lbaw2532.user_follows_campaign.user_id');

        // Merge and get unique user IDs, excluding campaign creator
        $recipientIds = $donorIds->merge($followerIds)
            ->unique()
            ->reject(fn($userId) => $userId == $campaign->creator_id);

        $content = "New update posted on '{$campaign->title}': {$update->title}";

        // Create notification for each recipient
        foreach ($recipientIds as $userId) {
            $notification = Notification::create([
                'content' => $content,
                'user_id' => $userId,
                'created_at' => now(),
                'is_read' => false,
            ]);

            NotificationCampaignUpdate::create([
                'notification_id' => $notification->notification_id,
                'update_id' => $update->update_id,
            ]);
        }
    }
}
