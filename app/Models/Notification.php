<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notification';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    protected $fillable = ['content', 'created_at', 'is_read', 'user_id'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the specific notification details (contribution, comment, campaign update, or collaboration request).
     */
    public function getDetails()
    {
        if ($contribution = NotificationContribution::find($this->notification_id)) {
            return $contribution;
        }
        if ($comment = NotificationComment::find($this->notification_id)) {
            return $comment;
        }
        if ($update = NotificationCampaignUpdate::find($this->notification_id)) {
            return $update;
        }
        if ($collabRequest = NotificationCollaborationRequest::find($this->notification_id)) {
            return $collabRequest;
        }
        return null;
    }

    /**
     * Get the campaign link for this notification.
     */
    public function getCampaignLink()
    {
        $details = $this->getDetails();
        
        if ($details instanceof NotificationContribution && $details->donation) {
            // Link directly to the donation on the campaign page so it can be highlighted
            $base = route('campaigns.show', $details->donation->campaign_id);
            return $base . '#donation-' . $details->donation->donation_id;
        }
        
        if ($details instanceof NotificationComment && $details->comment) {
            // Link directly to the comment on the campaign page so it can be highlighted
            $base = route('campaigns.show', $details->comment->campaign_id);
            return $base . '#comment-' . $details->comment->comment_id;
        }
        
        if ($details instanceof NotificationCampaignUpdate && $details->campaignUpdate) {
            // Link directly to the update on the campaign page so it can be highlighted
            $base = route('campaigns.show', $details->campaignUpdate->campaign_id);
            return $base . '#update-' . $details->campaignUpdate->update_id;
        }

        if ($details instanceof NotificationCollaborationRequest && $details->collaborationRequest) {
            // Link to the campaign page where the user can accept/reject the request
            return route('campaigns.show', $details->collaborationRequest->campaign_id);
        }
        
        return null;
    }
}
