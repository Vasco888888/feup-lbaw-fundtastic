<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnbanAppeal extends Model
{
    protected $table = 'unban_appeal';
    protected $primaryKey = 'appeal_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'reason',
        'date',
        'status',
    ];
}
