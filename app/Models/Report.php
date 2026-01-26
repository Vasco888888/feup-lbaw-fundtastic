<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    // Database uses singular table name `report`.
    protected $table = 'report';
    protected $primaryKey = 'report_id';
    public $timestamps = false;

    // Only include fields that exist on the current `report` table.
    protected $fillable = [
        'user_id',
        'comment_id',
        'campaign_id',
        'reason',
        'date',
        'status',
    ];
}
