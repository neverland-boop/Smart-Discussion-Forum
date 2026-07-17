<?php
namespace App\Services;

use App\Models\Topic;
use App\Models\User;

class ModerationService
{
    public function warnParticipant($topicId, $targetUserId, User $actingUser)
    {
        $topic = Topic::findOrFail($topicId);

        if ($topic->user_id === $actingUser->id || $actingUser->hasRole('admin')) {
            $userToWarn = User::findOrFail($targetUserId);
            
            $blacklist = $userToWarn->blacklist()->firstOrCreate(
                ['user_id' => $targetUserId],
                ['warning_count' => 0, 'status' => 'ACTIVE']
            );

            $blacklist->increment('warning_count');

            if ($blacklist->warning_count >= 2) {
                $blacklist->update([
                    'status' => 'COMPLIANCE_PERIOD',
                    'expiry_date' => now()->addHours(48), 
                ]);
            }
            return ['success' => true, 'data' => $blacklist];
        }
        return ['success' => false, 'message' => 'Unauthorized'];
    }

    public function clearWarningsIfCompliant(User $user)
    {
        $blacklist = $user->blacklist;
        if ($blacklist && $blacklist->warning_count > 0) {
            $blacklist->update([
                'warning_count' => 0,
                'status' => 'ACTIVE',
                'expiry_date' => null,
            ]);
        }
    }
}