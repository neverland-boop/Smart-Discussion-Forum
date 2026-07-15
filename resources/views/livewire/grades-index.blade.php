<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Array of student rows stored in local component state memory
    public array $studentGrades = [
        ['id' => 'STU001', 'name' => 'Anthony Evans', 'quiz' => 'Quiz 1: OS Sync', 'score' => 88, 'total' => 100, 'grade' => 'A'],
        ['id' => 'STU002', 'name' => 'Patience Amara', 'quiz' => 'Quiz 1: OS Sync', 'score' => 74, 'total' => 100, 'grade' => 'B'],
        ['id' => 'STU003', 'name' => 'Francis Duncan', 'quiz' => 'Quiz 1: OS Sync', 'score' => 69, 'total' => 100, 'grade' => 'C'],
        ['id' => 'STU004', 'name' => 'Blessing Chidi', 'quiz' => 'Quiz 1: OS Sync', 'score' => 45, 'total' => 100, 'grade' => 'F'],
    ];

    // System benchmarks for the green helper card on the right
    public array $gradingScale = [
        ['grade' => 'A', 'range' => '80% - 100%', 'color' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'],
        ['grade' => 'B', 'range' => '70% - 79%', 'color' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400'],
        ['grade' => 'C', 'range' => '60% - 69%', 'color' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'],
        ['grade' => 'D', 'range' => '50% - 59%', 'color' => 'bg-orange-50 text-orange-700 dark:bg-orange-950/40 dark:text-orange-400'],
    ];

    /**
     * Automatically fired by Livewire whenever a value inside $studentGrades is edited.
     * This intercepts the array index change and calculates the matching letter grade.
     */
    public function updatedStudentGrades($value, $key): void
    {
        // Extract which student row index was just typed into
        // Example: If $key is "0.score", $index becomes 0
        $parts = explode('.', $key);
        if (count($parts) < 2 || $parts[1] !== 'score') {
            return;
        }
        
        $index = (int)$parts[0];
        $score = (int)$value;
        
        // Calculate and override the matching grade string
        if ($score >= 80) {
            $this->studentGrades[$index]['grade'] = 'A';
        } elseif ($score >= 70) {
            $this->studentGrades[$index]['grade'] = 'B';
        } elseif ($score >= 60) {
            $this->studentGrades[$index]['grade'] = 'C';
        } elseif ($score >= 50) {
            $this->studentGrades[$index]['grade'] = 'D';
        } else {
            $this->studentGrades[$index]['grade'] = 'F';
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <!-- Header Section -->
    <div>
        <h1 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100 tracking-tight">Grades Ledger</h1>
        <p class="text-xs font-semibold text-zinc-400 mt-1">Review student performance data, edit exam score matrices, and track changes in real time</p>
    </div>

    <!-- Main Dynamic Layout Container Split -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT WORKSPACE: Live Scoring Grid (8 out of 12 columns) -->
        <div class="xl:col-span-8 flex flex-col gap-4">
            
            <!-- Student Grades Log Table -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 font-bold text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="p-3">Student Details</th>
                            <th class="p-3">Assessment Activity</th>
                            <th class="p-3 text-center w-36">Raw Score (/100)</th>
                            <th class="p-3 text-center w-28">Calculated Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-medium text-zinc-700 dark:text-zinc-300">
                        @foreach($studentGrades as $index => $row)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/20 transition-colors">
                            <!-- Student Metadata -->
                            <td class="p-3">
                                <span class="block font-bold text-zinc-900 dark:text-white">{{ $row['name'] }}</span>
                                <span class="block text-[10px] font-mono text-zinc-400 mt-0.5">{{ $row['id'] }}</span>
                            </td>
                            
                            <!-- Class Identifier -->
                            <td class="p-3 text-zinc-500 dark:text-zinc-400 font-semibold">
                                {{ $row['quiz'] }}
                            </td>
                            
                            <!-- REAL-TIME INPUT: Uses wire:model.live to detect typing changes instantly -->
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5 mx-auto max-w-[100px]">
                                    <input 
                                        wire:model.live="studentGrades.{{ $index }}.score" 
                                        type="number" 
                                        min="0" 
                                        max="100"
                                        class="w-14 text-center bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-700 rounded-md py-1 font-bold text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                    />
                                    <span class="text-zinc-400 font-bold">/100</span>
                                </div>
                            </td>
                            
                            <!-- AUTOMATIC BADGE: Recalculates dynamically based on the updated state -->
                            <td class="p-3 text-center">
                                <span class="inline-block w-8 py-1 text-xs font-black rounded-md shadow-sm border border-black/5 dark:border-white/5 transition-all duration-200
                                    {{ $row['grade'] === 'A' ? 'bg-emerald-500 text-white' : '' }}
                                    {{ $row['grade'] === 'B' ? 'bg-blue-500 text-white' : '' }}
                                    {{ $row['grade'] === 'C' ? 'bg-amber-500 text-white' : '' }}
                                    {{ $row['grade'] === 'D' ? 'bg-orange-500 text-white' : '' }}
                                    {{ $row['grade'] === 'F' ? 'bg-red-500 text-white' : '' }}
                                ">
                                    {{ $row['grade'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT PANEL: Reference Grading Scale (4 out of 12 columns) -->
        <div class="xl:col-span-4 p-5 bg-[#52c48a] rounded-2xl flex flex-col gap-4 text-zinc-900 shadow-md">
            <div class="border-b border-zinc-950/10 pb-2 text-center">
                <h3 class="font-black text-sm uppercase tracking-wider text-zinc-950">Grading Scale Matrix</h3>
                <p class="text-[11px] text-zinc-800 font-semibold mt-0.5">System benchmark boundary metrics</p>
            </div>

            <div class="flex flex-col gap-2">
                @foreach($gradingScale as $scale)
                    <div class="flex items-center justify-between p-2.5 bg-white/90 dark:bg-zinc-900/90 rounded-xl border border-white/20 shadow-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 flex items-center justify-center text-xs font-black rounded-lg border border-black/5 {{ $scale['color'] }}">
                                {{ $scale['grade'] }}
                            </span>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Grade Passband</span>
                        </div>
                        <span class="text-xs font-mono font-extrabold text-zinc-900 dark:text-zinc-100">
                            {{ $scale['range'] }}
                        </span>
                    </div>
                @endforeach
                <div class="flex items-center justify-between p-2.5 bg-white/90 dark:bg-zinc-900/90 rounded-xl border border-white/20 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 flex items-center justify-center text-xs font-black rounded-lg border border-black/5 bg-red-50 text-red-700">
                            F
                        </span>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Fail Condition</span>
                    </div>
                    <span class="text-xs font-mono font-extrabold text-zinc-900 dark:text-zinc-100">0% - 49%</span>
                </div>
            </div>
        </div>

    </div>
</div>
