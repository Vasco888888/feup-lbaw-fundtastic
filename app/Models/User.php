<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;

// Import Eloquent relationship classes.
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Table name in the lbaw2532 schema.
    // Use only the table name here. Previously this included a dotted
    // prefix (e.g. "lbaw2532.user") which Laravel treats as a connection
    // name (connection.table). That caused errors like
    // "Database connection [lbaw2532] not configured." Using the plain
    // table name lets the app use the configured DB connection.
    protected $table = 'user';

    // Primary key column name.
    protected $primaryKey = 'user_id';

    // Disable default created_at and updated_at timestamps for this model.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * Only these fields may be filled using methods like create() or update().
     * This protects against mass-assignment vulnerabilities.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'banned',
        'profile_media_id',
        'external_profile_image',
        'google_id',
    ];

    /**
     * The attributes that should be hidden when serializing the model
     * (e.g., to arrays or JSON).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to a specific type.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            // Ensures password is always hashed automatically when set.
            'password' => 'hashed',
            'banned' => 'boolean',
        ];
    }

    /**
     * Send the password reset notification using custom email template.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Generate the password reset URL
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));

        // Send custom email
        Mail::to($this->email)->send(
            new ResetPasswordMail($resetUrl, $this->name)
        );
    }

    /**
     * Get the campaigns created by this user.
     *
     * Defines a one-to-many relationship:
     * a user can create multiple campaigns.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'creator_id', 'user_id');
    }

    /**
     * Get the donations made by this user.
     *
     * Defines a one-to-many relationship:
     * a user can make multiple donations.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'donator_id', 'user_id');
    }

    /**
     * Get the comments made by this user.
     *
     * Defines a one-to-many relationship:
     * a user can make multiple comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }

    /**
     * Get the reports filed by this user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id', 'user_id');
    }

    /**
     * Get the unban appeals submitted by this user.
     */
    public function unbanAppeals(): HasMany
    {
        return $this->hasMany(UnbanAppeal::class, 'user_id', 'user_id');
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    /**
     * Get all campaigns this user is following.
     *
     * Defines a many-to-many relationship:
     * a user can follow many campaigns, and a campaign can be followed by many users.
     */
    public function followedCampaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'lbaw2532.user_follows_campaign', 'user_id', 'campaign_id');
    }

    /**
     * Get all campaigns this user is collaborating on.
     *
     * Defines a many-to-many relationship:
     * a user can be a collaborator on multiple campaigns (up to 5 per campaign).
     */
    public function collaboratingCampaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_collaborators', 'user_id', 'campaign_id')
                    ->withPivot('added_at')
                    ->orderBy('campaign_collaborators.added_at', 'desc');
    }

    /**
     * Get all collaboration requests made by this user.
     *
     * Defines a one-to-many relationship:
     * a user can make multiple collaboration requests.
     */
    public function collaborationRequests(): HasMany
    {
        return $this->hasMany(CollaborationRequest::class, 'requester_id', 'user_id');
    }

    /**
     * Get the profile media for this user.
     *
     * Defines a one-to-one relationship:
     * a user can have one profile picture stored in the media table.
     */
    public function profileMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'profile_media_id', 'media_id');
    }

    /**
     * Get the cards owned by this user.
     *
     * Defines a one-to-many relationship:
     * a user can have multiple cards.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'user_id', 'user_id');
    }

    /**
     * Accessor for profile image.
     * Returns external OAuth profile image, uploaded profile media, or a default asset.
     */
    public function getProfileImageAttribute($value)
    {
        // First, check if there's an external profile image from OAuth providers (Google, etc.)
        if ($this->external_profile_image) {
            return $this->external_profile_image;
        }

        // Check if there's an uploaded profile picture via media
        if ($this->profile_media_id) {
            $media = $this->profileMedia;
            if ($media && $media->file_path) {
                return asset($media->file_path);
            }
        }

        // Use a default SVG avatar placed in public/images. Replace this file
        // if you prefer a different default avatar. The filename used is
        // `defaultpfp.svg`.
        return asset('images/defaultpfp.svg');
    }

    /**
     * Set the user's banned status and suspend/restore their campaigns.
     *
     * When banning: suspend all campaigns that are currently 'active'.
     * When unbanning: restore campaigns that are currently 'suspended'.
     *
     * @param bool $banned
     * @return void
     */
    public function setBanned(bool $banned): void
    {
        $this->banned = $banned;
        $this->save();

        try {
            if ($banned) {
                $this->campaigns()->where('status', 'active')->update(['status' => 'suspended']);
            } else {
                $this->campaigns()->where('status', 'suspended')->update(['status' => 'active']);
            }
        } catch (\Throwable $e) {
            // Swallow errors to avoid breaking admin flows; logging can be
            // added later if desired.
        }
    }

    /**
     * Cascade delete related records when a user is deleted.
     *
     * This ensures reports and unban appeals referencing the user
     * are removed and won't appear in the administrator pages.
     */
    protected static function booted()
    {
        static::deleting(function ($user) {
            try {
                $user->reports()->delete();
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $user->unbanAppeals()->delete();
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
}
