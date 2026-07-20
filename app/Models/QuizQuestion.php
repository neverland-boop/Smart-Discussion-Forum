<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_title',
        'category_class',
        'question_text',
        'options',
        'correct_option_index'
    ];

    // Automatically transitions JSON strings to clean PHP arrays on call
    protected $casts = [
        'options' => 'array'
    ];
}
