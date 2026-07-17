<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'creator_id',
        'group_id',
        'time_limit',
        'auto_submit',
        'start_time'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}