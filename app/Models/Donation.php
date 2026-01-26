<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Import Eloquent relationship classes.
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    // Table name.
    protected $table = 'donation';
    
    // Primary key column name.
    protected $primaryKey = 'donation_id';
    
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
        'amount',
        'date',
        'message',
        'is_anonymous',
        'is_valid',
        'donator_id',
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
            'amount' => 'decimal:2',
            'date' => 'datetime',
            'is_anonymous' => 'boolean',
            'is_valid' => 'boolean',
        ];
    }

    /**
     * Get the user who made this donation.
     *
     * Defines a many-to-one relationship:
     * a donation belongs to exactly one user (donator).
     */
    public function donator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donator_id', 'user_id');
    }

    /**
     * Get the campaign this donation belongs to.
     *
     * Defines a many-to-one relationship:
     * a donation belongs to exactly one campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }
}
