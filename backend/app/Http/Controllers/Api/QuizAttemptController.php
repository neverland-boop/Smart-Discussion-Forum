<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuizAttemptResource;
use App\Http\Resources\MarkResource;
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

    // POST /api/attempts/{attempt}/submit
    public function submit(Request $request, $attemptId)
    {
        $validated = $request->validate([
            'answers'        => 'required|array',
            'auto_submitted' => 'boolean'
        ]);

        $score = $this->quizService->submitAttempt(
            (int)$attemptId, 
            $validated['answers'], 
            $validated['auto_submitted'] ?? false
        );

        return response()->json([
            'message' => 'Quiz submitted successfully.',
            'score'   => $score
        ]);
    }
}