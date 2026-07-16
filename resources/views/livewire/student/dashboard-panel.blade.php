<?php
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\Quiz;
use App\Models\Post;

new class extends Component {
    // Stat Cards
    public $activeGroupsCount = 0;
    public $pendingQuizzesCount = 0;
    public $avgScore = 0; 
    public $unreadMsgsCount = 0;

    // Collections for UI sections
    public $myGroups = [];
    public $upcomingQuizzes = [];
    public $recentActivities = [];

    public function mount()
    {
        $user = Auth::user();

        // 1. Fetch My Groups (Using your current schema where user_id is in the groups table)
        // If you create a pivot table later, change this to: $user->groups()->latest()->take(4)->get();
        $this->myGroups = Group::where('user_id', $user->id)->latest()->take(4)->get();
        $this->activeGroupsCount = $this->myGroups->count();

        // 2. Fetch Pending Quizzes
        if ($this->activeGroupsCount > 0) {
            $groupIds = $this->myGroups->pluck('id');
            
            $pendingQuery = Quiz::whereIn('group_id', $groupIds)
                ->where('status', '!=', 'DRAFT')
                ->whereDoesntHave('attempts', function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->whereNotNull('submitted_at');
                });

            $this->pendingQuizzesCount = $pendingQuery->count();
            
            $this->upcomingQuizzes = $pendingQuery
                ->orderBy('start_time', 'asc')
                ->take(3)
                ->get();
        }

        // 3. Average Score (Calculated directly from your 'marks' table)
        $average = DB::table('marks')
            ->where('user_id', $user->id)
            ->avg('score');
            
        $this->avgScore = $average ? round($average, 1) : 0;

        // 4. Fetch Unread Messages
        $this->unreadMsgsCount = Post::where('created_at', '>=', now()->subDays(7))
            ->where('user_id', '!=', $user->id) // Don't count their own posts
            ->count();

        // 5. Recent Activity Placeholder
        $this->recentActivities = collect([]); 
    }
}; ?>

<div class="p-6 space-y-8 min-h-screen text-slate-50">
    
    <!-- Page Header & Action -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold tracking-wide text-white">Dashboard Overview</h2>
        <button wire:click="$dispatch('open-join-modal')" class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-md font-semibold flex items-center transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Join a Group
        </button>
    </div>

    <!-- 1. STAT CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Active Groups -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Active Groups</span>
                <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-3xl font-bold text-white mt-4">{{ $activeGroupsCount }}</p>
        </div>

        <!-- Pending Quizzes -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Pending Quizzes</span>
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <p class="text-3xl font-bold text-white mt-4">{{ $pendingQuizzesCount }}</p>
        </div>

        <!-- Avg. Score -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Avg. Score</span>
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <p class="text-3xl font-bold text-white mt-4">{{ $avgScore > 0 ? $avgScore . '%' : '-' }}</p>
        </div>

        <!-- Unread Msgs -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Unread Msgs (7days)</span>
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <p class="text-3xl font-bold text-white mt-4">{{ $unreadMsgsCount }}</p>
        </div>
    </div>

    <!-- 2. MAIN PANELS (Grid Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- My Groups -->
<!-- MY GROUPS CONTAINER -->
        <div class="col-span-2">
            <h2 class="text-lg font-bold text-white mb-4">MY GROUPS</h2>
            
            <!-- This container now has a fixed height and hides the scrollbar -->
            <div class="h-[300px] overflow-y-auto no-scrollbar space-y-4 pr-2">
                @foreach($myGroups as $group)
                    <div class="bg-slate-800 border border-slate-700 p-5 rounded-xl flex justify-between items-center shrink-0">
                        <div>
                            <h3 class="text-white font-bold">{{ $group->name }}</h3>
                            <p class="text-slate-400 text-sm">Group ID: {{ $group->id }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Add this to your CSS file or a <style> block to hide the scrollbar -->
        <style>
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
            .no-scrollbar {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
        </style>

        <!-- Upcoming Quizzes -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-bold tracking-wide uppercase text-white mb-4">Upcoming Quizzes</h3>
            <div class="space-y-3">
                @forelse($upcomingQuizzes as $quiz)
                    <div class="bg-slate-800/50 border border-slate-700 p-4 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                        <h4 class="text-white font-medium truncate">{{ $quiz->title }}</h4>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-slate-400">
                                {{ $quiz->start_time ? \Carbon\Carbon::parse($quiz->start_time)->diffForHumans() : 'No start time set' }}
                            </span>
                            <span class="text-xs bg-green-900/30 text-green-400 border border-green-800/50 px-2 py-0.5 rounded-full">Pending</span>
                        </div>
                    </div>
                @empty
                    <div class="border border-dashed border-slate-700 rounded-lg p-10 flex items-center justify-center text-center h-32">
                        <p class="text-slate-500">No pending quizzes.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 3. RECENT ACTIVITY -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-bold tracking-wide uppercase text-white mb-4">Recommended topics</h3>
        <div class="space-y-4">
            @forelse($recentActivities as $activity)
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-slate-300 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-300">{!! $activity->description !!}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 italic text-sm">Activity log is empty.</p>
            @endforelse
        </div>
    </div>
        <livewire:student.join-group-modal />
</div>