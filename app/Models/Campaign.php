<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Import Eloquent relationship classes.
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Campaign extends Model
{
    // Table name.
    protected $table = 'campaign';
    
    // Primary key column name.
    protected $primaryKey = 'campaign_id';
    
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
        'description',
        'goal_amount',
        'current_amount',
        'start_date',
        'end_date',
        'status',
        'popularity',
        'creator_id',
        'category_id',
        'cover_media_id',
    ];

    /**
     * The attributes that should be cast to a specific type.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'popularity' => 'integer',
        ];
    }

    /**
     * Ensure related notification rows referencing this campaign's updates
     * are removed before the campaign is deleted to avoid FK violations.
     */
    protected static function booted()
    {
        static::deleting(function ($campaign) {
            try {
                $updateIds = $campaign->updates()->pluck('update_id')->toArray();
                if (!empty($updateIds)) {
                    $notifIds = DB::table('notification_campaign_update')->whereIn('update_id', $updateIds)->pluck('notification_id')->toArray();
                    DB::table('notification_campaign_update')->whereIn('update_id', $updateIds)->delete();
                    if (!empty($notifIds)) {
                        DB::table('notification')->whereIn('notification_id', $notifIds)->delete();
                    }
                }
            } catch (\Throwable $e) {
                // ignore cleanup errors; let DB raise exceptions where appropriate
            }
        });
    }

    /**
     * Get the user who created this campaign.
     *
     * Defines a many-to-one relationship:
     * a campaign belongs to exactly one user (creator).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'user_id');
    }

    /**
     * Get the collaborators of this campaign.
     *
     * Defines a many-to-many relationship:
     * a campaign can have up to 5 collaborators.
     */
    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'campaign_collaborators', 'campaign_id', 'user_id')
                    ->withPivot('added_at')
                    ->orderBy('campaign_collaborators.added_at', 'asc');
    }

    /**
     * Get the category of this campaign.
     *
     * Defines a many-to-one relationship:
     * a campaign belongs to exactly one category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get all donations for this campaign.
     *
     * Defines a one-to-many relationship:
     * a campaign can have many donations. Donations are always
     * returned ordered by date (most recent first) for consistent display.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'campaign_id', 'campaign_id')->orderBy('date', 'desc');
    }

    /**
     * Get all comments for this campaign.
     *
     * Defines a one-to-many relationship:
     * a campaign can have many comments. Comments are always
     * returned ordered by date (most recent first) for consistent display.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'campaign_id', 'campaign_id')->orderBy('date', 'desc');
    }

    /**
     * Get all updates for this campaign.
     *
     * Defines a one-to-many relationship:
     * a campaign can have many updates. Updates are always
     * returned ordered by date (most recent first) for consistent display.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(CampaignUpdate::class, 'campaign_id', 'campaign_id')->orderBy('date', 'desc');
    }

    /**
     * Get all media files for this campaign.
     *
     * Defines a one-to-many relationship:
     * a campaign can have many media files. Media are always
     * returned ordered by upload date (most recent first) for consistent display.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'campaign_id', 'campaign_id')->orderBy('uploaded_at', 'desc');
    }

    /**
     * Cover image for this campaign (points to a Media row).
     */
    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id', 'media_id');
    }

    /**
     * Get all users following this campaign.
     *
     * Defines a many-to-many relationship:
     * a campaign can be followed by many users, and a user can follow many campaigns.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lbaw2532.user_follows_campaign', 'campaign_id', 'user_id');
    }

    /**
     * Get all collaboration requests for this campaign.
     *
     * Defines a one-to-many relationship:
     * a campaign can have many collaboration requests.
     */
    public function collaborationRequests(): HasMany
    {
        return $this->hasMany(CollaborationRequest::class, 'campaign_id', 'campaign_id')->orderBy('created_at', 'desc');
    }
}
