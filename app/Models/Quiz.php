<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title', 'description', 'creator_id', 'group_id', 
        'time_limit', 'auto_submit', 'start_time'
    ];

    protected $casts = [
        'auto_submit' => 'boolean',
        'start_time'  => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}