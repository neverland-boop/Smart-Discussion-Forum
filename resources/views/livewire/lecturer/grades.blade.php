<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public array $grades = [
        ['id' => 'STU001', 'name' => 'Anthony Evans', 'activity' => 'Quiz 1: OS Sync', 'score' => 88, 'grade' => 'A'],
        ['id' => 'STU002', 'name' => 'Patience Amara', 'activity' => 'Quiz 1: OS Sync', 'score' => 74, 'grade' => 'B'],
        ['id' => 'STU003', 'name' => 'Francis Duncan', 'activity' => 'Quiz 1: OS Sync', 'score' => 68, 'grade' => 'C'],
        ['id' => 'STU004', 'name' => 'Blessing Chidi', 'activity' => 'Quiz 1: OS Sync', 'score' => 45, 'grade' => 'F'],
    ];
}; ?>

<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-wide">Grades Ledger</h1>
        <p class="text-slate-400 mt-1">Review student performance data, edit exam score matrices, and track changes in real time.</p>
    </div>

    <div class="flex flex-col gap-8">
        
        <!-- Main Table Section -->
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-lg overflow-hidden" x-data="{ expanded: false }">
            <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-slate-200 font-bold uppercase tracking-wider text-sm">Class Gradebook</h3>
                <button type="button" x-on:click="expanded = !expanded" class="text-blue-400 hover:text-blue-300 text-xs font-bold transition flex items-center gap-1">
                    <span x-show="!expanded">View All →</span>
                    <span x-show="expanded" style="display: none;">Show Less ↑</span>
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[560px]">
                    <thead class="text-slate-400 text-xs uppercase border-b border-slate-700">
                        <tr>
                            <th class="p-3 sm:p-6">Student Details</th>
                            <th class="p-3 sm:p-6">Assessment Activity</th>
                            <th class="p-3 sm:p-6">Raw Score (/100)</th>
                            <th class="p-3 sm:p-6">Calculated Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 text-sm">
                        @foreach($grades as $grade)
                        <tr class="hover:bg-slate-700/30 transition" x-show="expanded || {{ $loop->index }} < 2">
                            <td class="p-3 sm:p-6">
                                <div class="font-bold text-white">{{ $grade['name'] }}</div>
                                <div class="text-slate-500 text-xs">{{ $grade['id'] }}</div>
                            </td>
                            <td class="p-3 sm:p-6 text-slate-300">{{ $grade['activity'] }}</td>
                            <td class="p-3 sm:p-6 font-mono text-white">{{ $grade['score'] }} / 100</td>
                            <td class="p-3 sm:p-6">
                                <span class="inline-block px-3 py-1 rounded font-bold text-xs 
                                    {{ $grade['grade'] === 'A' ? 'bg-green-600 text-white' : '' }}
                                    {{ $grade['grade'] === 'B' ? 'bg-blue-600 text-white' : '' }}
                                    {{ $grade['grade'] === 'C' ? 'bg-yellow-600 text-white' : '' }}
                                    {{ $grade['grade'] === 'F' ? 'bg-red-600 text-white' : '' }}">
                                    {{ $grade['grade'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grading Scale Matrix (Bottom Section) -->
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-lg">
    <h3 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">Grading Scale Matrix</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        @php
            $scales = [
                ['grade' => 'A', 'range' => '80% - 100%', 'color' => 'bg-green-600'],
                ['grade' => 'B', 'range' => '70% - 79%', 'color' => 'bg-blue-600'],
                ['grade' => 'C', 'range' => '60% - 69%', 'color' => 'bg-yellow-600'],
                ['grade' => 'D', 'range' => '50% - 59%', 'color' => 'bg-orange-600'],
                ['grade' => 'F', 'range' => '0% - 49%', 'color' => 'bg-red-600'],
            ];
        @endphp

        @foreach($scales as $scale)
        <div class="bg-slate-900 border border-slate-700 p-4 rounded-lg text-center flex flex-col items-center">
            <!-- Badge color matches the table badges -->
            <div class="w-12 h-10 {{ $scale['color'] }} rounded flex items-center justify-center text-white font-black text-lg mb-3">
                {{ $scale['grade'] }}
            </div>
            <div class="text-slate-200 font-bold text-sm">{{ $scale['range'] }}</div>
            <div class="text-slate-500 text-[10px] uppercase font-bold mt-1">Passband</div>
        </div>
        @endforeach
    </div>
</div>

    </div>
</div>