<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationCollaborationRequest extends Model
{
    protected $table = 'notification_collaboration_request';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    protected $fillable = ['notification_id', 'request_id'];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'notification_id');
    }

    public function collaborationRequest(): BelongsTo
    {
        return $this->belongsTo(CollaborationRequest::class, 'request_id', 'request_id');
    }
}
