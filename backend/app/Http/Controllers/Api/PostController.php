<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('topic', 'user')->get();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string', // Fixed: 'text' isn't a validation rule; use 'string'
            'topic_id' => 'required|exists:topics,id',
        ]);

        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetches ONE specific topic
        $post = Post::with(['topic', 'user'])->findOrFail($id);
        
        return new PostResource($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        $validated = $request->validate([
            'content' => 'required|string',
        ]);
        
        $post->update($validated);
        
        return new PostResource($post);
    }

    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        
        // Return a success message or an empty response
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}