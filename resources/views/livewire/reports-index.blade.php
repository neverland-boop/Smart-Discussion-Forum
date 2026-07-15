<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Top summary stats derived from platform metrics
    public array $performanceReports = [
        ['metric' => 'Average Test Score', 'value' => '72.4%', 'trend' => '+3.1% this week', 'color' => 'text-emerald-600'],
        ['metric' => 'Forum Response Rate', 'value' => '89.1%', 'trend' => 'Stable tracking', 'color' => 'text-blue-600'],
        ['metric' => 'At-Risk Student Alerts', 'value' => '02', 'trend' => '-1 from last month', 'color' => 'text-amber-500'],
    ];

    // Detailed breakdown tracking student activities matrix
    public array $studentPerformanceData = [
        ['name' => 'Anthony Evans', 'id' => 'STU001', 'quiz_avg' => '88%', 'posts' => 42, 'replies' => 112, 'standing' => 'Excellent'],
        ['name' => 'Patience Amara', 'id' => 'STU002', 'quiz_avg' => '74%', 'posts' => 18, 'replies' => 45, 'standing' => 'Good'],
        ['name' => 'Francis Duncan', 'id' => 'STU003', 'quiz_avg' => '61%', 'posts' => 9, 'replies' => 22, 'standing' => 'Satisfactory'],
        ['name' => 'Blessing Chidi', 'id' => 'STU004', 'quiz_avg' => '45%', 'posts' => 2, 'replies' => 4, 'standing' => 'At Risk'],
    ];

    public function exportCSV(): void
    {
        // Mock export action trigger
        session()->flash('status', 'CSV report compiled and sent to your downloads path successfully.');
    }
}; ?>

<div class="flex flex-col gap-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100 tracking-tight">Academic Reports</h1>
            <p class="text-xs font-semibold text-zinc-400 mt-1">Review aggregated performance analytics, classroom metrics, and student participation footprints</p>
        </div>
        <!-- Export Toolkit Action Items -->
        <div class="shrink-0 flex gap-2">
            <button wire:click="exportCSV" class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-colors cursor-pointer border-none flex items-center gap-2">
                <span>📊</span> Export CSV Sheet
            </button>
            <button onclick="window.print()" class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer border border-zinc-300 flex items-center gap-2">
                <span>🖨️</span> Print Overview
            </button>
        </div>
    </div>

    <!-- Alert Status Bar Feedback -->
    @if (session()->has('status'))
        <div class="text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 p-3 rounded-lg border border-emerald-200 dark:border-emerald-900/50">
            {{ session('status') }}
        </div>
    @endif

    <!-- Primary Dual Partition Grid Configuration -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT PANELS: Performance Analytics Log Rows (8 out of 12 columns) -->
        <div class="xl:col-span-8 flex flex-col gap-5">
            
            <!-- Horizontal Summary Metrics Cards Stack Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($performanceReports as $card)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-xl flex flex-col gap-1">
                        <span class="text-[10px] text-zinc-400 font-extrabold uppercase tracking-wider">{{ $card['metric'] }}</span>
                        <span class="text-xl font-black {{ $card['color'] }}">{{ $card['value'] }}</span>
                        <span class="text-[10px] text-zinc-400 font-bold">{{ $card['trend'] }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Detailed Student Metric Log Table Ledger -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                    <h3 class="font-black text-xs uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Class Performance & Footprint Ledger</h3>
                </div>
                
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-950/20 border-b border-zinc-150 dark:border-zinc-800 font-bold text-zinc-500 dark:text-zinc-400">
                        <tr>
                            <th class="p-3">Student Particulars</th>
                            <th class="p-3 text-center">Quiz Average</th>
                            <th class="p-3 text-center">Topics Created</th>
                            <th class="p-3 text-center">Discussion Replies</th>
                            <th class="p-3 text-center">Standing Evaluation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-medium text-zinc-700 dark:text-zinc-300">
                        @foreach($studentPerformanceData as $data)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/20 transition-colors">
                            <td class="p-3">
                                <span class="block font-bold text-zinc-900 dark:text-white">{{ $data['name'] }}</span>
                                <span class="block text-[10px] font-mono text-zinc-400 mt-0.5">{{ $data['id'] }}</span>
                            </td>
                            <td class="p-3 text-center font-mono font-bold text-zinc-800 dark:text-zinc-200">
                                {{ $data['quiz_avg'] }}
                            </td>
                            <td class="p-3 text-center font-semibold text-zinc-500 dark:text-zinc-400">
                                {{ $data['posts'] }}
                            </td>
                            <td class="p-3 text-center font-semibold text-zinc-500 dark:text-zinc-400">
                                {{ $data['replies'] }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full whitespace-nowrap
                                    {{ $data['standing'] === 'Excellent' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : '' }}
                                    {{ $data['standing'] === 'Good' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400' : '' }}
                                    {{ $data['standing'] === 'Satisfactory' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' : '' }}
                                    {{ $data['standing'] === 'At Risk' ? 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 font-black animate-pulse' : '' }}
                                ">
                                    {{ $data['standing'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <!-- RIGHT PANEL: Solid Emerald Strategy Evaluation Insights (4 out of 12 columns) -->
        <div class="xl:col-span-4 p-5 bg-[#52c48a] rounded-2xl flex flex-col gap-4 text-zinc-900 shadow-md min-h-[380px]">
            <div class="border-b border-zinc-950/10 pb-2 text-center">
                <h3 class="font-black text-sm uppercase tracking-wider text-zinc-950">Participation Analysis</h3>
                <p class="text-[11px] text-zinc-800 font-semibold mt-0.5">Automated instructional diagnostic insights</p>
            </div>

            <!-- Insights Analysis Narrative Container -->
            <div class="flex flex-col gap-3 font-semibold text-xs text-zinc-800 leading-relaxed">
                <div class="bg-white/90 dark:bg-zinc-900/90 p-4 rounded-xl shadow-sm flex flex-col gap-1">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-700 dark:text-emerald-400">Class Insight</span>
                    <p class="font-medium text-zinc-700 dark:text-zinc-300">
                        Discussion footprint rates have scaled up by **14%** following the process synchronization lecture milestone. 
                    </p>
                </div>

                <div class="bg-white/90 dark:bg-zinc-900/90 p-4 rounded-xl shadow-sm flex flex-col gap-1">
                    <span class="text-[10px] font-extrabold uppercase text-amber-700 dark:text-amber-400">Action Required</span>
                    <p class="font-medium text-zinc-700 dark:text-zinc-300">
                        **2 students** have logged interaction footprints falling below benchmark criteria maps. Targeted messages should be dispatched.
                    </p>
                </div>
            </div>

            <div class="text-[10px] text-zinc-900 font-bold opacity-80 text-center mt-auto leading-relaxed bg-white/20 p-2.5 rounded-lg border border-white/10">
                📊 Data sets update dynamically every 60 minutes based on database forum analytics loops.
            </div>
        </div>

    </div>
</div>
