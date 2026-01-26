<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    // Table name.
    protected $table = 'comment';
    
    // Primary key column name.
    protected $primaryKey = 'comment_id';
    
    // Disable default created_at and updated_at timestamps for this model.
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'text',
        'date',
        'user_id',
        'campaign_id',
    ];

    /**
     * The attributes that should be cast to a specific type.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    /**
     * Get the user who created this comment.
     *
     * Defines a many-to-one relationship:
     * a comment belongs to exactly one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the campaign this comment belongs to.
     *
     * Defines a many-to-one relationship:
     * a comment belongs to exactly one campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }
}
