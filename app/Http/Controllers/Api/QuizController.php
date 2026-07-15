<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuizResource;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    // POST /api/groups/{group}/quizzes
    public function store(Request $request, $groupId)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'description' => 'required|string',
            'time_limit'  => 'required|integer|min:1',
            'start_time'  => 'nullable|date',
            'auto_submit' => 'boolean'
        ]);

        // The Service handles the business logic
        $quiz = $this->quizService->createQuiz($validated, (int)$groupId);
        
        return new QuizResource($quiz);
    }

    // POST /api/attempts/{attempt}/submit
    public function submit(Request $request, $attemptId)
    {
        $validated = $request->validate([
            'answers'        => 'required|array',
            'auto_submitted' => 'boolean'
        ]);

        // Route the submission to the Service to handle scoring and Mark generation
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

    // GET /api/quizzes/{quiz}/report
    public function report($quizId)
    {
        // Fetch all marks for this specific quiz
        $marks = Mark::where('quiz_id', $quizId)->with('student')->get();
        
        return MarkResource::collection($marks);
    }

public function show($id)
    {
        $quiz = \App\Models\Quiz::with('questions')->findOrFail($id);

        // Block API access if it's too early
        if ($quiz->start_time && now()->isBefore($quiz->start_time)) {
            return response()->json([
                'success' => false, 
                'message' => 'This quiz has not started yet.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $quiz
        ]);
    }
}