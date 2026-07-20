<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * POST /api/attempts/{attemptId}/submit
     * Handles assessment submission, scoring, and server-side time limit enforcement.
     */
    public function submit(Request $request, $attemptId)
    {
        // 1. Validate incoming payload
        $validated = $request->validate([
            'answers'        => 'required|array',
            'auto_submitted' => 'boolean'
        ]);

        // 2. Fetch the attempt and its parent quiz
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);
        $quiz = $attempt->quiz;
        
        // 3. SERVER-SIDE CHEAT PROTECTION
        // Calculate the absolute latest time this attempt should be accepted.
        $quizEndTime = $attempt->start_time->copy()->addMinutes($quiz->time_limit);
        
        // If the server receives this payload AFTER the deadline, force the auto_submitted flag.
        // We add a 30-second grace period to account for network latency from the Java client.
        $isLate = now()->greaterThan($quizEndTime->addSeconds(30));
        $autoSubmitted = $isLate ? true : ($validated['auto_submitted'] ?? false);

        try {
            // 4. Pass to the unified Service Layer
            $score = $this->quizService->submitAttempt(
                (int)$attemptId, 
                $validated['answers'], 
                $autoSubmitted
            );

            return response()->json([
                'success' => true,
                'message' => $isLate ? 'Time expired. Assessment auto-submitted.' : 'Quiz submitted successfully.',
                'data'    => ['score' => $score]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process submission.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}