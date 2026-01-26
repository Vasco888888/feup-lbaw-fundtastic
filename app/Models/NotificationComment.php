<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationComment extends Model
{
    protected $table = 'notification_comment';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    protected $fillable = ['notification_id', 'comment_id'];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'notification_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
    }
}
