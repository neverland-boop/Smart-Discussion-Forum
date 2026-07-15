<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Mark;
use App\Models\User;

class QuizService
{
    // --- 1. QUIZ CREATION (Your existing code, optimized for API) ---
    public function createQuiz(array $data, int $groupId, User $creator): Quiz
    {
        return Quiz::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'time_limit'  => $data['time_limit'],
            'start_time'  => $data['start_time'],
            'auto_submit' => $data['auto_submit'] ?? true,
            'creator_id'  => $creator->id, // Replaced Auth::id() for API compatibility
            'group_id'    => $groupId,
        ]);
    }

    // --- 2. QUIZ SUBMISSION (Your existing code) ---
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

    // --- 3. FETCHING QUIZZES (New: For the UI and Desktop App) ---
    public function getAvailableQuizzes()
    {
        return Quiz::where('status', 'ANNOUNCED')->latest()->get();
    }

    // --- 4. USER STATISTICS (New: Wired up to your real Mark model!) ---
    public function getUserStats(User $user)
    {
        // Actually count how many marks this user has
        $completedCount = Mark::where('user_id', $user->id)->count();
        
        // Calculate their actual average score
        $averageScore = Mark::where('user_id', $user->id)->avg('score') ?? 0;
        
        return [
            'completed_count' => $completedCount, 
            'average_score'   => round($averageScore, 1) . '%',
            'pending_reviews' => 0, // Update this later if you add manual grading
        ];
    }
}