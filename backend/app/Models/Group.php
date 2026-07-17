<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function topics() {
        return $this->hasMany(Topic::class);
    }

    public function members() {
    return $this->belongsToMany(User::class, 'group_members');
}
}

