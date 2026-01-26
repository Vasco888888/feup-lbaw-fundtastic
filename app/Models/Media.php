<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    // Table name
    protected $table = 'media';

    // Primary key
    protected $primaryKey = 'media_id';

    // No created_at / updated_at timestamps in the SQL schema
    public $timestamps = false;

    // Mass assignable (minimal set)
    protected $fillable = [
        'file_path',
        'media_type',
        'uploaded_at',
        'campaign_id',
        'user_id',
    ];


    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    
    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }
}
