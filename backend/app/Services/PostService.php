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
                ->select('id', 'topic_id', 'user_id', 'content', 'created_at') 
                ->with('author:id,name,avatar,bio')
                ->latest()
                ->take(50)
                ->get()
                ->reverse(); 
        }

    public function createPost(array $data, int $topicId): Post
        {
            $post = Post::create([
                'user_id'     => Auth::id(),
                'topic_id'    => $topicId,
                'content'     => $data['content'],
            ]);
            Topic::where('id', $topicId)->increment('post_count');

            return $post;
        }
}