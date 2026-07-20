<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TopicService;
use App\Services\ModerationService;
use App\Services\PostService;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function store(Request $request, TopicService $topicService)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'group_id' => 'required|exists:groups,id',
            'is_private' => 'boolean',
        ]);

        $topic = $topicService->createTopic($validated, $request->user());
        return response()->json(['success' => true, 'data' => $topic], 201);
    }

    public function requestAccess(Request $request, TopicService $topicService)
    {
        $request->validate(['topic_id' => 'required|exists:topics,id']);
        $topicService->requestAccess($request->topic_id, $request->user());
        return response()->json(['success' => true, 'message' => 'Access requested.']);
    }

    public function approve(Request $request, TopicService $topicService)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $approved = $topicService->approveParticipant($request->topic_id, $request->user_id, $request->user());
        
        if ($approved) {
            return response()->json(['success' => true, 'message' => 'Participant approved.']);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function warn(Request $request, ModerationService $moderationService)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $result = $moderationService->warnParticipant($request->topic_id, $request->user_id, $request->user());
        
        if ($result['success']) {
            return response()->json($result);
        }
        return response()->json(['error' => $result['message']], 403);
    }

    public function sendMessage(Request $request, PostService $postService, ModerationService $moderationService)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'content' => 'required|string|max:2000'
        ]);

        $postService->createPost(['content' => $request->content], $request->topic_id);
        
        // Trigger compliance
        $moderationService->clearWarningsIfCompliant($request->user());

        return response()->json(['success' => true, 'message' => 'Message sent.']);
    }

    /**
     * Handles the PDF / Transcript export feature for discussion threads 
     * matching the requirement: "memebers can easily choose to see details... export to PDF"[cite: 1, 2].
     */
}