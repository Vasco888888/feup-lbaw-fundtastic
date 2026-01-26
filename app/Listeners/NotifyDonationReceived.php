<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use App\Models\Notification;
use App\Models\NotificationContribution;

class NotifyDonationReceived
{
    public function handle(DonationReceived $event): void
    {
        $donation = $event->donation;
        $campaign = $donation->campaign;
        
        // Don't notify if no campaign or if campaign creator is the donor
        if (!$campaign || $donation->donator_id == $campaign->creator_id) {
            return;
        }

        // Check if notification already exists for this donation to avoid duplicates
        $existing = NotificationContribution::where('donation_id', $donation->donation_id)->first();
        if ($existing) {
            return;
        }

        $donatorName = $donation->is_anonymous 
            ? 'an anonymous supporter' 
            : ($donation->donator->name ?? 'Someone');

        $content = "You received a €" . number_format($donation->amount, 2) 
                 . " donation from {$donatorName} on '{$campaign->title}'";

        // Create notification
        $notification = Notification::create([
            'content' => $content,
            'user_id' => $campaign->creator_id,
            'created_at' => now(),
            'is_read' => false,
        ]);

        // Link to donation
        NotificationContribution::create([
            'notification_id' => $notification->notification_id,
            'donation_id' => $donation->donation_id,
        ]);
    }
}
