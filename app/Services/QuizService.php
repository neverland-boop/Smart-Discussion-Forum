<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Mark;
use Illuminate\Support\Facades\Auth;

class QuizService
{
    public function createQuiz(array $data, int $groupId): Quiz
    {
        return Quiz::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'time_limit'  => $data['time_limit'],
            'start_time'  => $data['start_time'],
            'auto_submit' => $data['auto_submit'] ?? true,
            'creator_id'  => Auth::id(),
            'group_id'    => $groupId,
        ]);
    }

    public function submitAttempt(int $attemptId, array $answers, bool $autoSubmitted = false)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        
        // 1. Close the attempt record
        $attempt->update([
            'submitted_at'   => now(),
            'answers'        => $answers,
            'auto_submitted' => $autoSubmitted,
        ]);

        // 2. Calculate the score (Placeholder logic)
        $calculatedScore = 0; 
        
        // 3. Save the official mark to the independent Marks table
        Mark::create([
            'user_id' => $attempt->user_id,
            'quiz_id' => $attempt->quiz_id,
            'score'   => $calculatedScore,
        ]);

        return $calculatedScore;
    }
}