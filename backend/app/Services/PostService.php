<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function getPostsByTopic(int $topicId)
        {
            return Post::query()
                ->where('topic_id', $topicId)
                // 1. SELECTIVE FETCHING: Only grab the exact columns we need for the UI
                ->select('id', 'topic_id', 'user_id', 'content', 'created_at') 
                // 2. OPTIMIZED EAGER LOADING: Only fetch the 'id' and 'name' from the author/user table
                ->with('author:id,name,avatar,bio') // 👈 Add avatar and bio here
                // 3. SORT & LIMIT: Let the database do the heavy lifting
                ->latest()
                ->take(50)
                ->get()
                // 4. REVERSE: Flip the collection for the chat interface
                ->reverse(); 
        }

    public function createPost(array $data, int $topicId): Post
        {
            $post = Post::create([
                'user_id'     => Auth::id(),
                'topic_id'    => $topicId,
                'content'     => $data['content'],
            ]);

            // Directly increment the post_count without fetching the Topic model first
            Topic::where('id', $topicId)->increment('post_count');

            return $post;
        }
}