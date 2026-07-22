<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use App\Models\Report;
use Illuminate\Http\Request;


class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    // GET /api/topics/{topic}/posts
    public function index($topicId)
    {
        $posts = $this->postService->getPostsByTopic((int)$topicId);
        return PostResource::collection($posts);
    }

    // POST /api/topics/{topic}/posts
    public function store(Request $request, $topicId)
    {
        $validated = $request->validate([
            'content'     => 'required|string',
            'receiver_id' => 'nullable|integer|exists:users,id'
        ]);

        $post = $this->postService->createPost($validated, (int)$topicId);

        if (auth()->user()->warning_count > 0) {
            auth()->user()->pardon();
        }
        // --- NEW LOGIC: Inactivity Auto-Pardon ---
        // If a student had warnings for inactivity but finally posted,
        // automatically reset their warnings back to zero.
        if (auth()->user()->warning_count > 0) {
            auth()->user()->pardon();
        }

        return new PostResource($post);
    }

// --- ADD THIS ENTIRE METHOD ---
    // POST /api/posts/{post}/flag
    public function flag(Request $request, $postId)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        Report::create([
            'post_id' => (int)$postId,
            'reported_by' => auth()->id(),
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Message has been flagged and sent to the administrator for review.'
        ]);
    }
}