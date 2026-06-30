<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    public function group() {
    return $this->belongsTo(Group::class);
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }

    protected $fillable = [
    description,
    group_id,
    is_locked
    ];

    protected $casts = [
        'is_locked' => 'boolean'
    ];
}


