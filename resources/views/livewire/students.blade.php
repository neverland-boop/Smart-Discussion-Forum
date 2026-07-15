<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Synchronized student dataset matching the reports tab matrix parameters
    public array $students = [
        ['id' => 'STU001', 'name' => 'Anthony Evans', 'email' => 'anthony@example.com', 'standing' => 'Excellent'],
        ['id' => 'STU002', 'name' => 'Patience Amara', 'email' => 'patience@example.com', 'standing' => 'Good'],
        ['id' => 'STU003', 'name' => 'Francis Duncan', 'email' => 'francis@example.com', 'standing' => 'Satisfactory'],
        ['id' => 'STU004', 'name' => 'Blessing Chidi', 'email' => 'blessing@example.com', 'standing' => 'At Risk'],
    ];
}; ?>

<div class="flex flex-col gap-6">
    <!-- Header Section -->
    <div>
        <h1 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100 tracking-tight">Students Directory</h1>
        <p class="text-xs font-semibold text-zinc-400 mt-1">Manage active student profiles, review academic standings, and track platform enrollment records</p>
    </div>

    <!-- Primary Layout Grid: Table on left, Info panel on right -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT PANEL: Matched Students List Table (8 out of 12 columns) -->
        <div class="xl:col-span-8 flex flex-col gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                    <h3 class="font-black text-xs uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Class Roster ({{ count($students) }} Total Enrolled)</h3>
                </div>

                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-950/20 border-b border-zinc-150 dark:border-zinc-800 font-bold text-zinc-500 dark:text-zinc-400">
                        <tr>
                            <th class="p-3">Student Details</th>
                            <th class="p-3">Email Address</th>
                            <th class="p-3 text-center">Current Standing</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-medium text-zinc-700 dark:text-zinc-300">
                        @foreach($students as $student)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/20 transition-colors">
                            <!-- Student Name & System ID -->
                            <td class="p-3">
                                <span class="block font-bold text-zinc-900 dark:text-white">{{ $student['name'] }}</span>
                                <span class="block text-[10px] font-mono text-zinc-400 mt-0.5">{{ $student['id'] }}</span>
                            </td>
                            
                            <!-- Email -->
                            <td class="p-3 text-zinc-500 dark:text-zinc-400 font-semibold">
                                {{ $student['email'] }}
                            </td>
                            
                            <!-- Matching Standing Badge -->
                            <td class="p-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full whitespace-nowrap
                                    {{ $student['standing'] === 'Excellent' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : '' }}
                                    {{ $student['standing'] === 'Good' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400' : '' }}
                                    {{ $student['standing'] === 'Satisfactory' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' : '' }}
                                    {{ $student['standing'] === 'At Risk' ? 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 font-black animate-pulse' : '' }}
                                ">
                                    {{ $student['standing'] }}
                                </span>
                            </td>

                            <!-- Action Triggers -->
                            <td class="p-3 text-center">
                                <button class="text-zinc-500 hover:text-zinc-800 text-[10px] font-bold px-2 py-1 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm cursor-pointer transition-colors">
                                    View Log
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT PANEL: Solid Emerald Roster Controls (4 out of 12 columns) -->
        <div class="xl:col-span-4 p-5 bg-[#52c48a] rounded-2xl flex flex-col gap-4 text-zinc-900 shadow-md min-h-[340px]">
            <div class="border-b border-zinc-950/10 pb-2 text-center">
                <h3 class="font-black text-sm uppercase tracking-wider text-zinc-950">Roster Management</h3>
                <p class="text-[11px] text-zinc-800 font-semibold mt-0.5">Administrative workspace overrides</p>
            </div>

            <!-- Context Actions Stack -->
            <div class="flex flex-col gap-3 font-semibold text-xs text-zinc-800 leading-relaxed">
                <div class="bg-white/90 dark:bg-zinc-900/90 p-4 rounded-xl shadow-sm flex flex-col gap-2">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-700 dark:text-emerald-400">Class Actions</span>
                    <button class="w-full bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs py-2 rounded-lg cursor-pointer transition-colors border-none text-center">
                        + Enroll New Student
                    </button>
                </div>

                <div class="bg-white/90 dark:bg-zinc-900/90 p-4 rounded-xl shadow-sm flex flex-col gap-1.5">
                    <span class="text-[10px] font-extrabold uppercase text-zinc-500">Directory Context</span>
                    <p class="font-medium text-zinc-600 dark:text-zinc-400 text-[11px]">
                        Profiles synchronized seamlessly across reports logs, scoring grids, and chat monitoring streams.
                    </p>
                </div>
            </div>

            <div class="text-[10px] text-zinc-900 font-bold opacity-80 text-center mt-auto leading-relaxed bg-white/20 p-2 rounded-lg border border-white/10">
                👥 Roster updates will dynamically refresh security tracking token logs.
            </div>
        </div>

    </div>
</div>
