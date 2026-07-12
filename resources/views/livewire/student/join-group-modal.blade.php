<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public bool $showJoinModal = false;

    #[On('open-join-modal')]
    public function showModal()
    {
        $this->showJoinModal = true;
    }

    // 1. THIS IS THE METHOD THAT WAS MISSING
    public function joinGroup($groupId)
    {
        $user = Auth::user();

        // Safety Guard: Check if the user is already a member
        if ($user->groups()->where('group_id', $groupId)->exists()) {
            return; // Stop here if they are already in the group
        }

        // Attach them if they aren't
        $user->groups()->attach($groupId);
        
        $this->show = false;
        $this->dispatch('group-joined'); 
    }

// In JoinGroupModal.php
    public function with(): array
    {
        return [
            'availableGroups' => Group::whereDoesntHave('members', function($query) {
                $query->where('user_id', Auth::id());
            })->get(),
        ];
    }
}; 
?>

<div>
    @if($showJoinModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 p-4">
            <div class="bg-slate-800 rounded-xl p-6 w-full max-w-md shadow-2xl border border-slate-700">
                <h2 class="text-xl font-bold text-white mb-4">Available Groups</h2>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($availableGroups as $group)
                        <div class="flex items-center justify-between p-3 bg-slate-900 rounded-lg">
                            <span class="text-slate-200">{{ $group->name }}</span>
                            <button wire:click="joinGroup({{ $group->id }})" class="px-3 py-1 bg-green-600 hover:bg-green-500 text-white text-sm rounded-md transition">
                                Join
                            </button>
                        </div>
                    @empty
                        <p class="text-slate-500 text-center">No new groups to join right now.</p>
                    @endforelse
                </div>

                <button wire:click="$set('showJoinModal', false)" class="mt-6 w-full py-2 text-slate-400 hover:text-white transition">
                    Cancel
                </button>
            </div>
        </div>
    @endif
</div>