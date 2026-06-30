<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Http\Resources\TopicResource;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::with('group', 'posts')->get();
        return TopicResource::collection($topics);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
        ]);
    }

    /**
     * Display the specified resource.
     */
public function show(string $id)
{
    // Fetches ONE specific topic
    $topic = Topic::with(['group', 'posts'])->findOrFail($id);
    
    return new TopicResource($topic);
}

public function update(Request $request, $id)
{
    $topic = Topic::findOrFail($id);
    
    $validated = $request->validate([
        'title' => 'string|max:255',
        'is_locked' => 'boolean',
    ]);
    
    $topic->update($validated);
    
    return new TopicResource($topic);
}

public function destroy($id)
{
    $topic = Topic::findOrFail($id);
    $topic->delete();
    
    // Return a success message or an empty response
    return response()->json(['message' => 'Topic deleted successfully'], 200);
}
}
