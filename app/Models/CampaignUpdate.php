<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Import Eloquent relationship classes.
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignUpdate extends Model
{
    // Table name in the lbaw2532 schema.
    protected $table = 'campaign_update';
    
    // Primary key column name.
    protected $primaryKey = 'update_id';
    
    // Disable default created_at and updated_at timestamps for this model.
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * Only these fields may be filled using methods like create() or update().
     * This protects against mass-assignment vulnerabilities.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'date',
        'campaign_id',
        'author_id',
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
     * Get the campaign this update belongs to.
     *
     * Defines a many-to-one relationship:
     * an update belongs to exactly one campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    /**
     * Get the user who authored this update.
     *
     * Defines a many-to-one relationship:
     * an update belongs to exactly one user (author).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }
}
