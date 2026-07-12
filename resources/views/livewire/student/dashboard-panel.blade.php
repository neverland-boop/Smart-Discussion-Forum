<?php
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'stats' => [
                'groups' => 0,
                'quizzes_due' => 0,
                'avg_score' => '-',
                'unread_messages' => 0,
            ],
            'my_groups' => [], 
            'upcoming_quizzes' => [], 
            'recent_activities' => [] 
        ];
    }
}; ?>

<!-- ADDED p-6 lg:p-8 HERE to push content away from the edges -->
<div class="max-w-7xl mx-auto space-y-6 p-6 lg:p-8">
    
    <!-- Functional Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-white tracking-tight">Dashboard Overview</h1>
        <a href="{{ route('forums') ?? '#' }}" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white text-sm font-medium rounded-lg transition shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Join a Group
        </a>
    </div>

    <!-- STATS ROW -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Active Groups</p>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mt-2">{{ $stats['groups'] }}</h3>
        </div>

        <!-- Card 2 -->
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Pending Quizzes</p>
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mt-2">{{ $stats['quizzes_due'] }}</h3>
        </div>

        <!-- Card 3 -->
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Avg. Score</p>
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mt-2">{{ $stats['avg_score'] }}</h3>
        </div>
        
        <!-- Card 4 -->
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Unread Msgs</p>
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mt-2">{{ $stats['unread_messages'] }}</h3>
        </div>
    </div>

    <!-- MIDDLE SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: My Groups -->
        <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-xl p-5 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">My Groups</h2>
            </div>
            
            <div class="space-y-2 flex-1">
                @forelse($my_groups as $group)
                    <div class="flex items-center justify-between p-3 bg-slate-900/30 border border-slate-700/50 rounded-lg hover:bg-slate-700 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-slate-700 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($group['name'], 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-slate-200 text-sm font-semibold">{{ $group['name'] }}</h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                    <span>{{ $group['members'] }} members</span>
                                    @if($group['new_msgs'] > 0)
                                        <span>•</span>
                                        <span class="text-green-400 font-medium">{{ $group['new_msgs'] }} new</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Subtle Zero Data State -->
                    <div class="flex items-center justify-center h-24 border border-dashed border-slate-600 rounded-lg bg-slate-800/30">
                        <p class="text-sm text-slate-500">No active groups. <a href="{{ route('forums') ?? '#' }}" class="text-green-400 hover:text-green-300 font-medium ml-1">Browse groups →</a></p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Upcoming Quizzes -->
        <div class="lg:col-span-1 bg-slate-800 border border-slate-700 rounded-xl p-5 shadow-sm flex flex-col">
            <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider mb-4">Upcoming Quizzes</h2>
            <div class="space-y-2 flex-1">
                @forelse($upcoming_quizzes as $quiz)
                    @php
                        $border = $quiz['urgency'] === 'high' ? 'border-red-500/50' : ($quiz['urgency'] === 'medium' ? 'border-orange-500/50' : 'border-emerald-500/50');
                        $text = $quiz['urgency'] === 'high' ? 'text-red-400' : ($quiz['urgency'] === 'medium' ? 'text-orange-400' : 'text-emerald-400');
                    @endphp
                    
                    <div class="p-3 bg-slate-900/30 border-l-2 {{ $border }} border-y border-r border-slate-700/50 rounded-lg">
                        <h4 class="text-slate-200 font-medium text-sm truncate">{{ $quiz['title'] }}</h4>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-[11px] text-slate-400 truncate w-24">{{ $quiz['group'] }}</p>
                            <p class="text-[11px] font-semibold {{ $text }}">{{ $quiz['due_text'] }}</p>
                        </div>
                    </div>
                @empty
                    <!-- Subtle Zero Data State -->
                    <div class="flex items-center justify-center h-24 border border-dashed border-slate-600 rounded-lg bg-slate-800/30">
                        <p class="text-sm text-slate-500">No pending quizzes.</p>
                    </div>
                @endforelse
            </div>
        </div>
        
    </div>

    <!-- BOTTOM SECTION: Recent Activity -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 shadow-sm">
        <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider mb-4">Recent Activity</h2>
        <div class="space-y-3">
            @forelse($recent_activities as $activity)
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-slate-600 flex-shrink-0"></div>
                    <div class="flex-1 flex justify-between items-center">
                        <div>
                            <h4 class="text-slate-200 text-sm">{{ $activity['title'] }}</h4>
                            <p class="text-[11px] text-slate-400">{{ $activity['group'] }}</p>
                        </div>
                        <span class="text-[11px] text-slate-500">{{ $activity['time'] }}</span>
                    </div>
                </div>
            @empty
                 <!-- Subtle Zero Data State -->
                <p class="text-sm text-slate-500 italic">Activity log is empty.</p>
            @endforelse
        </div>
    </div>
<livewire:student.create-group-modal />
</div>