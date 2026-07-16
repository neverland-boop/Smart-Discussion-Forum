<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Mark;
use App\Models\User;

class QuizService
{
    // --- 1. QUIZ CREATION ---
    public function createQuiz(array $data, int $groupId, User $creator): Quiz
    {
        return Quiz::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'time_limit'  => $data['time_limit'],
            'start_time'  => $data['start_time'],
            'auto_submit' => $data['auto_submit'] ?? true,
            'creator_id'  => $creator->id, 
            'group_id'    => $groupId,
        ]);
    }

    // --- 2. QUIZ SUBMISSION & SCORING ---
    public function submitAttempt(int $attemptId, array $answers, bool $autoSubmitted = false)
    {
        // Eager load the quiz and its questions to compare answers
        $attempt = QuizAttempt::where('id', $attemptId)
        ->whereNull('submitted_at')
        ->firstOrFail();
        
        // 1. Close the attempt record
        $attempt->update([
            'submitted_at'   => now(),
            'answers'        => json_encode($answers), // Ensure it's stored as JSON
            'auto_submitted' => $autoSubmitted,
        ]);

        // 2. Calculate the score
        $totalEarned = 0;
        $totalPossible = 0;

        foreach ($attempt->quiz->questions as $question) {
            $totalPossible += $question->points;
            
            // Check if the user answered this question and if it matches the correct_answer
            if (isset($answers[$question->id]) && $answers[$question->id] == $question->correct_answer) {
                $totalEarned += $question->points;
            }
        }

        // Calculate percentage (avoid division by zero if quiz has no questions)
        $calculatedScore = $totalPossible > 0 ? (int) round(($totalEarned / $totalPossible) * 100) : 0;
        
        // 3. Save the official mark to the independent Marks table
        // We use updateOrCreate to respect your unique constraint ['user_id', 'quiz_id']
        Mark::updateOrCreate(
            [
                'user_id' => $attempt->user_id,
                'quiz_id' => $attempt->quiz_id,
            ],
            [
                'score' => $calculatedScore,
            ]
        );

        return $calculatedScore;
    }

    // --- 3. FETCHING QUIZZES ---
    public function getAvailableQuizzes()
    {
        return Quiz::where('status', 'ANNOUNCED')->latest()->get();
    }

    // --- 4. USER STATISTICS ---
    public function getUserStats(User $user)
    {
        $completedCount = Mark::where('user_id', $user->id)->count();
        $averageScore = Mark::where('user_id', $user->id)->avg('score') ?? 0;
        
        return [
            'completed_count' => $completedCount, 
            'average_score'   => round($averageScore, 1) . '%',
            'pending_reviews' => 0, 
        ];
    }

    // --- 5. RECENT RESULTS (New for Dashboard) ---
    public function getRecentResults(User $user, int $limit = 5)
    {
        // Fetch the user's marks, including the quiz details, ordered by completion date
        return Mark::with('quiz')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->take($limit)
            ->get();
    }
}