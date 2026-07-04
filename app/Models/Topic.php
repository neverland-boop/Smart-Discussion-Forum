<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'title', 'description', 'group_id', 'user_id', 
        'post_count', 'classification'
    ];

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }
}