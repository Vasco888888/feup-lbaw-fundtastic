<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Import Eloquent relationship classes.
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // Table name.
    protected $table = 'category';
    
    // Primary key column name.
    protected $primaryKey = 'category_id';
    
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
        'name',
        'description',
    ];

    /**
     * Get all campaigns in this category.
     *
     * Defines a one-to-many relationship:
     * a category can have many campaigns.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'category_id', 'category_id');
    }
}
