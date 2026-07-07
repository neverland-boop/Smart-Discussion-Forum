<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id', 'user_id', 'start_time', 'submitted_at', 
        'answers', 'auto_submitted'
    ];

    protected $casts = [
        'start_time'     => 'datetime',
        'submitted_at'   => 'datetime',
        'auto_submitted' => 'boolean',
        'answers'        => 'array', 
    ];
}