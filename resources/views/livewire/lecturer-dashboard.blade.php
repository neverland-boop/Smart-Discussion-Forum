<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public string $department = 'Computer Science & Academic Forum Moderator';
    public string $welcomeGreeting = '';
    
    public array $activeTopics = [
        ['id' => 1, 'title' => 'Advanced Operating Systems - Process Synchronization', 'replies' => 24, 'status' => 'Active', 'code' => 'CS_4102', 'time' => '12m ago'],
        ['id' => 2, 'title' => 'Database Systems - Normalization Paradigms (3NF vs BCNF)', 'replies' => 41, 'status' => 'Review Needed', 'code' => 'CS_3301', 'time' => '2h ago']
    ];
    
    public array $pendingFlags = [
        ['id' => 101, 'user' => 'Student_Anthony', 'reason' => 'Irrelevant Link (Rule #2 Violation)', 'preview' => 'Check out this crypto trade setup guys...']
    ];

    public int $flagCount = 0;
    public string $newChannelCode = '';
    public string $newChannelTitle = '';

    public function mount(): void
    {
        date_default_timezone_set('Africa/Kampala');
        $hour = (int) date('H');
        
        if ($hour < 12) {
            $this->welcomeGreeting = 'Good morning';
        } elseif ($hour < 17) {
            $this->welcomeGreeting = 'Good afternoon';
        } else {
            $this->welcomeGreeting = 'Good evening';
        }

        $this->flagCount = count($this->pendingFlags);
    }

    public function createChannel(): void
    {
        $this->validate([
            'newChannelCode' => 'required|string|max:10',
            'newChannelTitle' => 'required|string|max:255'
        ]);

        $this->activeTopics[] = [
            'id' => count($this->activeTopics) + 1,
            'title' => $this->newChannelTitle,
            'replies' => 0,
            'status' => 'Active',
            'code' => strtoupper($this->newChannelCode),
            'time' => 'Just now'
        ];

        $this->newChannelCode = '';
        $this->newChannelTitle = '';

        $this->dispatch('channel-created');
    }
}; ?>

<div x-data="{ showCreateModal: false }" @channel-created.window="showCreateModal = false" class="flex flex-col gap-6 relative">
    
    <!-- WELCOMING MESSAGE BANNER -->
    <div class="p-6 bg-gradient-to-r from-zinc-900 to-zinc-800 text-white rounded-2xl border border-zinc-800 shadow-md relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="z-10 flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-[#52c48a] rounded-full inline-block animate-pulse"></span>
                <h1 class="text-xl font-black tracking-tight text-white">
                    {{ $welcomeGreeting }}, {{ auth()->user()->name ?? 'Busingye' }}!
                </h1>
            </div>
            <p class="text-xs text-zinc-400 font-semibold max-w-xl leading-relaxed">
                Welcome back to your control workspace. All forum channels, active academic threads, and security gateway monitoring flags are live and synced.
            </p>
        </div>

        <div class="z-10 flex gap-4 shrink-0">
            <div class="bg-zinc-800/60 backdrop-blur border border-zinc-700/60 px-4 py-2.5 rounded-xl text-center min-w-[100px] shadow-sm">
                <span class="block text-xl font-black text-[#52c48a]">{{ count($activeTopics) }}</span>
                <span class="block text-[9px] text-zinc-400 font-extrabold uppercase tracking-wider mt-0.5">Active Channels</span>
            </div>
            <div class="bg-zinc-800/60 backdrop-blur border border-zinc-700/60 px-4 py-2.5 rounded-xl text-center min-w-[100px] shadow-sm">
                <span class="block text-xl font-black text-amber-400">{{ $flagCount }}</span>
                <span class="block text-[9px] text-zinc-400 font-extrabold uppercase tracking-wider mt-0.5">Critical Flags</span>
            </div>
        </div>
    </div>

    <!-- Main Workspace Split Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 w-full items-start">
        
        <!-- LEFT WORKSPACE: Academic Forum Channels -->
        <div class="xl:col-span-7 p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex flex-col justify-between gap-6 min-h-[440px]">
            <div>
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-200">Active Discussion Forums</h2>
                        <p class="text-[11px] text-zinc-400 font-medium">Manage threads and reply interaction permissions</p>
                    </div>
                    <button type="button" @click="showCreateModal = true" class="bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-[10px] px-3 py-1.5 rounded-xl shadow transition-colors cursor-pointer border-none">
                        + Create Channel
                    </button>
                </div>

                <!-- Channel Items List -->
                <div class="space-y-3">
                    @foreach($activeTopics as $topic)
                        <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-4">
                            <div class="truncate">
                                <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider block mb-0.5">{{ $topic['code'] }}</span>
                                <h3 class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $topic['title'] }}</h3>
                                <span class="text-[10px] text-zinc-400 font-medium">{{ $topic['replies'] }} student interactions recorded • Last post {{ $topic['time'] }}</span>
                            </div>
                            <button type="button" class="text-zinc-500 hover:text-zinc-800 text-[11px] font-bold px-2.5 py-1 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm cursor-pointer transition-colors">Manage</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Global Forum Advisory Notice Console -->
            <div class="bg-zinc-50 dark:bg-zinc-950 rounded-xl p-4 border border-zinc-200 dark:border-zinc-800">
                <label for="notice" class="text-[10px] font-black text-zinc-500 uppercase tracking-wider block mb-1.5">Broadcast System Advisory Notice</label>
                <textarea id="notice" placeholder="Publish an administrative statement directly to the top banners of all active room streams..." class="w-full bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 h-16 focus:outline-none focus:ring-2 focus:ring-zinc-500 shadow-inner"></textarea>
                <div class="mt-2 flex justify-end">
                    <button type="button" class="bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-[10px] tracking-wide px-4 py-1.5 rounded-lg shadow transition-all cursor-pointer border-none">Publish Notification</button>
                </div>
            </div>
        </div>

        <!-- RIGHT WORKSPACE: Solid Emerald Rules Moderation Stack -->
        <div class="xl:col-span-5 p-5 bg-[#52c48a] rounded-2xl flex flex-col justify-between gap-6 min-h-[440px] shadow-sm">
            <div class="flex flex-col gap-4 grow">
                <div class="border-b border-zinc-950/10 pb-3 text-center">
                    <h3 class="font-black text-sm text-zinc-950 uppercase tracking-wider">Gateway Rules Monitoring</h3>
                    <p class="text-[11px] text-zinc-800 font-semibold mt-0.5">Live student evaluation queue & activity reports</p>
                </div>

                <div class="space-y-3 max-h-[240px] overflow-y-auto pr-1">
                    @foreach($pendingFlags as $flag)
                        <div class="bg-white/95 dark:bg-zinc-900/95 rounded-xl p-4 border border-white/20 shadow-md flex flex-col gap-2">
                            <div class="flex justify-between items-center text-[10px] font-bold">
                                <span class="text-zinc-500 dark:text-zinc-400">User: <strong class="text-zinc-900 dark:text-white">{{ $flag['user'] }}</strong></span>
                                <span class="text-red-600 font-extrabold uppercase tracking-tight bg-red-50 px-1.5 py-0.5 rounded">Flagged Content</span>
                            </div>
                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 leading-tight">{{ $flag['reason'] }}</p>
                            <span class="block text-[11px] italic bg-zinc-100 dark:bg-zinc-950 text-zinc-500 p-2 rounded border-l-2 border-amber-500 truncate">"{{ $flag['preview'] }}"</span>
                            <div class="flex justify-end gap-2 mt-1 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                <button type="button" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-bold text-[10px] px-3 py-1.5 rounded-lg cursor-pointer border-none shadow-sm">Dismiss</button>
                                <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold text-[10px] px-3 py-1.5 rounded-lg cursor-pointer border-none shadow-sm">Apply Ban</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- POPUP MODAL WIREFRAME COMPONENT LAYER -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" style="display: none;">
