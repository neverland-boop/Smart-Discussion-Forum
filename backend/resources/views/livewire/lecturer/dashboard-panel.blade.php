<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public string $welcomeGreeting = '';
    public array $activeTopics = [
        ['id' => 1, 'title' => 'Advanced Operating Systems - Process Synchronization', 'replies' => 24, 'code' => 'CS_4102', 'time' => '12m ago'],
        ['id' => 2, 'title' => 'Database Systems - Normalization Paradigms (3NF vs BCNF)', 'replies' => 41, 'code' => 'CS_3301', 'time' => '2h ago']
    ];
    public array $pendingFlags = [
        ['id' => 101, 'user' => 'Student_Anthony', 'reason' => 'Irrelevant Link (Rule #2 Violation)', 'preview' => 'Check out this crypto trade setup guys...']
    ];

    public function mount(): void {
        $hour = (int) date('H');
        $this->welcomeGreeting = ($hour < 12) ? 'Good morning' : (($hour < 17) ? 'Good afternoon' : 'Good evening');
    }
}; ?>

<div x-data="{ showCreateModal: false }" class="flex flex-col gap-8 max-w-5xl mx-auto">

    <!-- WELCOMING BANNER -->
    <div class="bg-zinc-900 text-white rounded-2xl p-5 sm:p-8 border border-zinc-800 flex flex-col md:flex-row md:items-center md:justify-between gap-6 shadow-xl">
        <div>
            <h1 class="text-xl sm:text-2xl font-black">{{ $welcomeGreeting }}, {{ auth()->user()->name ?? 'Lecturer' }}!</h1>
            <p class="text-sm text-zinc-400">Welcome back. All forum channels and gateway monitoring systems are live.</p>
        </div>
        <div class="flex gap-6">
            <div class="text-center">
                <div class="text-2xl font-black text-emerald-500">{{ count($activeTopics) }}</div>
                <div class="text-[9px] uppercase font-bold text-zinc-500 tracking-wider">Active Channels</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-black text-amber-400">{{ count($pendingFlags) }}</div>
                <div class="text-[9px] uppercase font-bold text-zinc-500 tracking-wider">Critical Flags</div>
            </div>
        </div>
    </div>

    <!-- MAIN VERTICAL WORKSPACE -->
    <div class="flex flex-col gap-8">
        
        <!-- SECTION 1: FORUMS -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 sm:p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-sm font-black uppercase text-zinc-900 dark:text-white tracking-widest">Active Discussion Forums</h2>
                    <p class="text-xs text-zinc-500 mt-1">Manage threads and reply interaction permissions.</p>
                </div>
                <button @click="showCreateModal = true" class="w-full sm:w-auto bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold px-6 py-3 rounded-xl transition">+ Create New Channel</button>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4">
                @foreach($activeTopics as $topic)
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                        <div>
                            <div class="text-[10px] font-black text-blue-600 uppercase">{{ $topic['code'] }}</div>
                            <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $topic['title'] }}</div>
                        </div>
                        <button class="text-xs font-bold text-zinc-400 hover:text-zinc-900 dark:hover:text-white">Manage</button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SECTION 2: GATEWAY MONITORING -->
<!-- SECTION 2: GATEWAY MONITORING (Matching your exact green) -->
<div class="bg-[#52c48a] rounded-2xl p-5 sm:p-8 shadow-lg">
    <div class="flex items-center gap-4 mb-8">
        <!-- Icon container uses a slightly darker shade for depth -->
        <div class="bg-[#3dae75] p-3 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div>
            <!-- Using text-zinc-950 for better contrast against the green -->
            <h3 class="text-zinc-950 font-black text-lg uppercase tracking-wider">Gateway Rules Monitoring</h3>
            <p class="text-zinc-900/80 text-xs font-semibold">Live student evaluation queue & activity reports.</p>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($pendingFlags as $flag)
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-md flex flex-col md:flex-row gap-6 items-start md:items-center">
                <div class="flex-1">
                    <span class="text-[10px] font-black text-red-600 uppercase bg-red-50 px-2 py-1 rounded">Flagged Content</span>
                    <div class="font-bold text-zinc-900 mt-2">User: {{ $flag['user'] }}</div>
                    <div class="text-sm text-zinc-600">{{ $flag['reason'] }}</div>
                    <div class="italic text-zinc-400 text-xs mt-1">"{{ $flag['preview'] }}"</div>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <button class="flex-1 md:flex-none bg-zinc-100 text-zinc-600 text-xs font-bold px-6 py-3 rounded-lg hover:bg-zinc-200 transition">Dismiss</button>
                    <button class="flex-1 md:flex-none bg-red-600 text-white text-xs font-bold px-6 py-3 rounded-lg hover:bg-red-700 transition">Apply Ban</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
        </div>
    </div>
</div>