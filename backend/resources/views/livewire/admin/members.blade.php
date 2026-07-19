<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Report;

new class extends Component {
    public $activeTab = 'members';

    public function with()
    {
        return [
            'users' => User::role('student')->paginate(15),
            'lecturers' => User::role('lecturer')->get(),
            'reports' => Report::where('is_resolved', false)->latest()->get(),
        ];
    }

    // --- Moderation Methods ---
    public function confirmFlag($reportId) {
        $report = Report::find($reportId);
        if ($report && $report->post) {
            $user = $report->post->user;
            $user->issueWarning(); 
            $report->post->delete();
        }
        $report->update(['is_resolved' => true]);
    }

    public function dismissFlag($reportId) {
        Report::find($reportId)->update(['is_resolved' => true]);
    }

    // --- User Management Methods ---
    public function issueWarning($userId) {
        User::find($userId)->issueWarning();
    }

    public function pardon($userId) {
        User::find($userId)->pardon();
    }

    public function manualSuspend($userId) {
        $user = User::find($userId);
        $user->blacklist()->updateOrCreate(
            ['user_id' => $user->id],
            ['status' => 'SUSPENDED', 'expiry_date' => now()->addDays(7)]
        );
    }
}; ?>

<div class="p-6 text-gray-200" x-data="{ activeTab: @entangle('activeTab') }">
    <!-- Tab Navigation -->
    <div class="flex border-b border-gray-700 mb-6">
        <button @click="activeTab = 'members'" 
                class="px-6 py-3 border-b-2 font-medium transition"
                :class="activeTab === 'members' ? 'border-green-500 text-green-500' : 'text-gray-400 border-transparent'">
            Members
        </button>
        <button @click="activeTab = 'lecturers'" 
                class="px-6 py-3 border-b-2 font-medium transition"
                :class="activeTab === 'lecturers' ? 'border-green-500 text-green-500' : 'text-gray-400 border-transparent'">
            Lecturers
        </button>
        <button @click="activeTab = 'moderation'" 
                class="px-6 py-3 border-b-2 font-medium transition"
                :class="activeTab === 'moderation' ? 'border-green-500 text-green-500' : 'text-gray-400 border-transparent'">
            Moderation ({{ $reports->count() }})
        </button>
    </div>

    <!-- MEMBERS TAB -->
    <div x-show="activeTab === 'members'" x-cloak>
        <div class="bg-gray-800 rounded-lg shadow border border-gray-700">
            <table class="w-full text-left">
                <thead class="bg-gray-900 text-gray-300">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Warnings</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-750">
                            <td class="p-4">{{ $user->name }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs rounded {{ $user->status === 'ACTIVE' ? 'bg-green-900' : 'bg-red-900' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="p-4">{{ $user->warning_count }}</td>
                            <td class="p-4 text-right space-x-2">
                                <button wire:click="issueWarning({{ $user->id }})" class="text-orange-400 hover:text-orange-300 text-sm">Warn</button>
                                <button wire:click="manualSuspend({{ $user->id }})" class="text-red-400 hover:text-red-300 text-sm">Suspend</button>
                                <button wire:click="pardon({{ $user->id }})" class="text-green-400 hover:text-green-300 text-sm">Pardon</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

 <!-- LECTURERS TAB -->
<div x-show="activeTab === 'lecturers'" x-cloak x-data="{ showModal: false }">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Faculty Members</h2>
        <button @click="showModal = true" class="bg-green-600 px-4 py-2 rounded text-white font-medium hover:bg-green-700">
            Add Lecturer
        </button>
    </div>

    <!-- The Modal Overlay -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
        <div @click.away="showModal = false" class="w-full max-w-md">
            <livewire:admin.register-lecturer-modal />
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg shadow border border-gray-700 p-4">
        @foreach($lecturers as $lecturer)
            <div class="flex justify-between p-3 border-b border-gray-700 last:border-0">
                <span>{{ $lecturer->name }}</span>
                <span class="text-gray-400">{{ $lecturer->email }}</span>
            </div>
        @endforeach
    </div>
</div>

    <!-- MODERATION TAB -->
    <div x-show="activeTab === 'moderation'" x-cloak>
        <div class="bg-gray-800 rounded-lg shadow border border-gray-700">
            <table class="w-full text-left">
                <thead class="bg-gray-900 text-gray-300">
                    <tr>
                        <th class="p-4">Post</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-750">
                            <td class="p-4 italic text-gray-400">"{{ Str::limit($report->post->content ?? 'Deleted', 50) }}"</td>
                            <td class="p-4 text-right space-x-3">
                                <button wire:click="confirmFlag({{ $report->id }})" class="text-red-400 font-bold">Confirm</button>
                                <button wire:click="dismissFlag({{ $report->id }})" class="text-gray-400">Dismiss</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="p-8 text-center text-gray-500">No flags.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>