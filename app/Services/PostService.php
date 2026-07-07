<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function getPostsByTopic(int $topicId)
    {
        return Post::where('topic_id', $topicId)
            ->with('author')
            ->orderBy('created_at', 'asc') // Chat streams flow chronologically
            ->get();
    }

    public function createPost(array $data, int $topicId): Post
    {
        $post = Post::create([
            'user_id'     => Auth::id(),
            'topic_id'    => $topicId,
            'content'     => $data['content'],
        ]);

        // Increment running total counter inside parent topic entity
        $topic = Topic::find($topicId);
        if ($topic) {
            $topic->increment('post_count');
        }

        return $post;
    }
}