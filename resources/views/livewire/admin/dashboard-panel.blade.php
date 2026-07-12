<?php
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            // Requirement 7: Each group gets its own statistics
            'groupStats' => [
                ['name' => 'Software Engineering', 'members' => 45, 'posts_today' => 120, 'status' => 'Highly Active'],
                ['name' => 'Database Admin', 'members' => 32, 'posts_today' => 15, 'status' => 'Moderate'],
                ['name' => 'UI/UX Design', 'members' => 28, 'posts_today' => 2, 'status' => 'Dormant'],
            ],
            // Requirement 4: Warnings and blacklisting monitor
            'inactiveUsers' => [
                ['name' => 'John Doe', 'last_seen' => '14 days ago', 'warnings' => 1, 'status' => 'Warning Sent'],
                ['name' => 'Jane Smith', 'last_seen' => '30 days ago', 'warnings' => 2, 'status' => 'Final Warning'],
                ['name' => 'Mark Taylor', 'last_seen' => '45 days ago', 'warnings' => 2, 'status' => 'Blacklisted'],
            ]
        ];
    }
}; ?>

<div>
    <h2 class="text-2xl font-bold mb-6 text-white border-b border-slate-800 pb-4">Group Statistics</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        @foreach($groupStats as $group)
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 hover:border-green-500 transition">
            <h3 class="text-lg font-bold text-white mb-2">{{ $group['name'] }}</h3>
            <div class="space-y-2 mt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Total Members</span>
                    <span class="text-white font-medium">{{ $group['members'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Posts Today</span>
                    <span class="text-white font-medium">{{ $group['posts_today'] }}</span>
                </div>
                <div class="flex justify-between text-sm mt-4 pt-4 border-t border-slate-700">
                    <span class="text-slate-400">Activity Level</span>
                    <span class="font-bold {{ $group['status'] === 'Highly Active' ? 'text-emerald-400' : ($group['status'] === 'Dormant' ? 'text-red-400' : 'text-amber-400') }}">
                        {{ $group['status'] }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-bold mb-6 text-white border-b border-slate-800 pb-4">Inactivity & Blacklist Monitor</h2>
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-900/50 text-slate-400 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4">Member Name</th>
                    <th class="px-6 py-4">Last Seen</th>
                    <th class="px-6 py-4">Warnings Issued</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($inactiveUsers as $user)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 font-medium text-white">{{ $user['name'] }}</td>
                    <td class="px-6 py-4">{{ $user['last_seen'] }}</td>
                    <td class="px-6 py-4 text-amber-400 font-bold">{{ $user['warnings'] }} / 2</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-md {{ $user['status'] === 'Blacklisted' ? 'bg-red-900/50 text-red-400' : 'bg-amber-900/50 text-amber-400' }}">
                            {{ $user['status'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($user['status'] === 'Blacklisted')
                            <button class="text-emerald-400 hover:text-emerald-300 font-semibold">Lift Ban</button>
                        @else
                            <button class="text-green-400 hover:text-green-300 font-semibold">Issue Warning</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>