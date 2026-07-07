<?php

namespace App\Services;

use App\Models\Topic;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;

class TopicService
{
    public function getTopicsByGroup(int $groupId)
    {
        // Enforce enrollment rule
        $isEnrolled = GroupMember::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'User lacks group enrollment credentials.');
        }

        return Topic::where('group_id', $groupId)
            ->with(['author'])
            ->latest()
            ->get();
    }

    public function createTopic(array $data, int $groupId): Topic
    {
        return Topic::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'group_id'    => $groupId,
            'user_id'     => Auth::id(),
            'post_count'  => 0,
        ]);
    }
}