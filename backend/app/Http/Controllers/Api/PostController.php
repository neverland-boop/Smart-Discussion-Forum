<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Services\PostService;
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
        return new PostResource($post);
    }
}