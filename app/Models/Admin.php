<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    // Table name in DB is singular `admin`.
    protected $table = 'admin';

    // Primary key is `admin_id`.
    protected $primaryKey = 'admin_id';

    // No automatic Laravel `updated_at` column in the schema.
    public $timestamps = false;

    // Mass assignable attributes for convenience.
    protected $fillable = [
        'email',
        'password',
        'name',
        'bio',
        'profile_image',
    ];

    // Hide sensitive attributes when serializing.
    protected $hidden = [
        'password',
    ];

    /**
     * Accessor for profile image.
     * Return stored profile_image or a default asset if not set.
     */
    public function getProfileImageAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Default file name for the shared default avatar (SVG).
        return asset('images/defaultpfp.svg');
    }
}
