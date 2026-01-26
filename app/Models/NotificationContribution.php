<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationContribution extends Model
{
    protected $table = 'notification_contribution';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    protected $fillable = ['notification_id', 'donation_id'];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'notification_id');
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'donation_id', 'donation_id');
    }
}
