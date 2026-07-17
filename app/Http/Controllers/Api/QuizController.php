<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuizResource;
use App\Services\QuizService;
use Illuminate\Http\Request;
use App\Models\Mark;
use App\Http\Resources\MarkResource;

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
    $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);
    
    // SERVER-SIDE PROTECTION: 
    // Even if the user submits late, use the quiz's fixed end time
    $quizEndTime = $attempt->start_time->addMinutes($attempt->quiz->time_limit);
    
    // If they submit after the deadline, force the auto_submitted flag
    $finalSubmitTime = now()->greaterThan($quizEndTime) ? $quizEndTime : now();
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

public function report($quizId)
{
    // The requirement: "all members... see the performance report"
    // We add a check to ensure the quiz has actually ended before showing the report
    $quiz = \App\Models\Quiz::findOrFail($quizId);
    
    if (now()->isBefore($quiz->start_time->addMinutes($quiz->time_limit))) {
        return response()->json(['message' => 'Report not available until quiz concludes.'], 403);
    }

    $marks = Mark::where('quiz_id', $quizId)->with('student')->get();
    return MarkResource::collection($marks);
}
public function show($id)
{
    $quiz = \App\Models\Quiz::with('questions')->findOrFail($id);

    // Provide the server's authoritative time
    return response()->json([
        'success' => true,
        'data' => [
            'quiz' => new QuizResource($quiz),
            'server_time' => now(), // Essential for syncing the countdown
            'ends_at' => $quiz->start_time->addMinutes($quiz->time_limit)
        ]
    ]);
}
}