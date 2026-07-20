<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Mark;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizService
{
    // --- 1. QUIZ & QUESTION CREATION (Transaction Protected) ---
    public function createQuiz(array $quizData, array $questionsData, int $groupId, int $creatorId): Quiz
    {
        // Use a database transaction to ensure both Quiz and Questions save together securely.
        return DB::transaction(function () use ($quizData, $questionsData, $groupId, $creatorId) {
            
            // 1. Create the Quiz Parent Record
            $quiz = Quiz::create([
                'title'       => $quizData['title'],
                'description' => $quizData['description'],
                'status'      => $quizData['status'] ?? 'DRAFT',
                'time_limit'  => $quizData['time_limit'],
                'start_time'  => !empty($quizData['start_time']) ? Carbon::parse($quizData['start_time']) : null,
                'auto_submit' => $quizData['auto_submit'] ?? true,
                'creator_id'  => $creatorId, 
                'group_id'    => $groupId,
            ]);

            // 2. Prepare Questions for Bulk Insertion
            $questionsToInsert = [];
            foreach ($questionsData as $q) {
                $questionsToInsert[] = [
                    'quiz_id'        => $quiz->id,
                    'text'           => $q['text'],
                    'options'        => json_encode($q['options']), // Encode A,B,C,D map to JSON
                    'correct_answer' => $q['correct_answer'],
                    'points'         => $q['points'] ?? 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            // 3. Bulk Insert Questions
            DB::table('questions')->insert($questionsToInsert);

            // Return the quiz with its newly created questions attached
            return $quiz->load('questions');
        });
    }

    // --- 2. QUIZ SUBMISSION & SCORING ---
    public function submitAttempt(int $attemptId, array $answers, bool $autoSubmitted = false)
    {
        // Eager load the quiz and its questions to compare answers
        $attempt = QuizAttempt::with('quiz.questions')
            ->where('id', $attemptId)
            ->whereNull('submitted_at')
            ->firstOrFail();
        
        // 1. Close the attempt record
        $attempt->update([
            'submitted_at'   => now(),
            'answers'        => json_encode($answers), // Store exact user payload
            'auto_submitted' => $autoSubmitted,
        ]);

        // 2. Calculate the score
        $totalEarned = 0;
        $totalPossible = 0;

        foreach ($attempt->quiz->questions as $question) {
            $totalPossible += $question->points;
            
            // Validate user answer against the correct_answer key (e.g., "B")
            if (isset($answers[$question->id]) && strtoupper(trim($answers[$question->id])) === strtoupper(trim($question->correct_answer))) {
                $totalEarned += $question->points;
            }
        }

        // Calculate percentage (avoid division by zero)
        $calculatedScore = $totalPossible > 0 ? (int) round(($totalEarned / $totalPossible) * 100) : 0;
        
        // 3. Save the official mark to the independent Marks table
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
        return Quiz::where('status', 'PUBLISHED')->latest()->get(); // Adjusted to PUBLISHED based on standard schema
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

    // --- 5. RECENT RESULTS ---
    public function getRecentResults(User $user, int $limit = 5)
    {
        return Mark::with('quiz')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->take($limit)
            ->get();
    }
}