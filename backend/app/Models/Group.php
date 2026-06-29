<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function topics(){
        return $this->hasmany(Topic::class);
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id');
    }
}
