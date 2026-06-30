<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function topic(){
        return $this->belongsTo(Topic::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        group_id,
        author_id,
        content,
        creator_id
    ];
}
