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
}
