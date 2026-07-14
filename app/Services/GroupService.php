<?php
namespace App\Services;

use App\Models\Group;
use App\Models\User;

class GroupService
{
    public function joinGroup($groupId, User $user)
    {
        $user->groups()->syncWithoutDetaching([$groupId]);
        return ['success' => true, 'message' => 'Successfully joined the group.'];
    }

    public function getUserGroups(User $user)
    {
        return $user->groups()->with('topics')->latest()->get();
    }

    public function getAvailableGroups(User $user)
    {
        return Group::whereDoesntHave('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->take(10)->get();
    }
}