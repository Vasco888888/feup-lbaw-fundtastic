<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationCampaignUpdate extends Model
{
    protected $table = 'notification_campaign_update';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    protected $fillable = ['notification_id', 'update_id'];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'notification_id');
    }

    public function campaignUpdate(): BelongsTo
    {
        return $this->belongsTo(CampaignUpdate::class, 'update_id', 'update_id');
    }
}
