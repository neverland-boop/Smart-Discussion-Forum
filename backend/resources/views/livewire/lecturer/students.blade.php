<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public array $students = [
        ['id' => 'STU001', 'name' => 'Anthony Evans', 'email' => 'anthony@example.com', 'standing' => 'Excellent'],
        ['id' => 'STU002', 'name' => 'Patience Amara', 'email' => 'patience@example.com', 'standing' => 'Good'],
        ['id' => 'STU003', 'name' => 'Francis Duncan', 'email' => 'francis@example.com', 'standing' => 'Satisfactory'],
        ['id' => 'STU004', 'name' => 'Blessing Chidi', 'email' => 'blessing@example.com', 'standing' => 'At Risk'],
    ];
}; ?>

<div class="p-4 sm:p-8 max-w-7xl mx-auto min-h-screen">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-wide">Students Directory</h1>
        <p class="text-slate-400 mt-1">Manage active student profiles, review academic standings, and track platform enrollment records.</p>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Expanded Table Section with Inline Expand (Spans 2 columns) -->
        <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-xl shadow-lg overflow-hidden" 
             x-data="{ expanded: false }">
            
            <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-slate-200 font-bold uppercase tracking-wider text-sm">
                    Class Roster ({{ count($students) }} Total Enrolled)
                </h3>
                <!-- Toggle Button for Dynamic Expansion -->
                <button type="button" 
                        x-on:click="expanded = !expanded" 
                        class="text-blue-400 hover:text-blue-300 text-xs font-bold transition focus:outline-none flex items-center gap-1">
                    <span x-show="!expanded">View All →</span>
                    <span x-show="expanded" style="display: none;">Show Less ↑</span>
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[480px]">
                    <thead class="text-slate-400 text-xs uppercase border-b border-slate-700">
                        <tr>
                            <th class="p-3 sm:p-6">Student Details</th>
                            <th class="p-3 sm:p-6">Email Address</th>
                            <th class="p-3 sm:p-6 text-center">Standing</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 text-sm">
                        @foreach($students as $student)
                        <!-- Shows only the first 2 students initially, reveals all when expanded is true -->
                        <tr class="hover:bg-slate-700/30 transition" 
                            x-show="expanded || {{ $loop->index }} < 2"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2">
                            
                            <td class="p-3 sm:p-6">
                                <div class="font-bold text-white">{{ $student['name'] }}</div>
                                <div class="text-slate-500 text-xs">{{ $student['id'] }}</div>
                            </td>
                            <td class="p-3 sm:p-6 text-slate-300">{{ $student['email'] }}</td>
                            <td class="p-3 sm:p-6 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold 
                                    {{ $student['standing'] === 'Excellent' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $student['standing'] === 'Good' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                    {{ $student['standing'] === 'Satisfactory' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                    {{ $student['standing'] === 'At Risk' ? 'bg-red-500/20 text-red-400' : '' }}">
                                    {{ $student['standing'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Management Sidebar (Spans 1 column) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-lg">
                <h3 class="text-white font-bold mb-4">Roster Management</h3>
                <button class="w-full bg-green-600 hover:bg-green-500 text-white py-3 rounded-lg font-bold text-sm transition shadow-lg mb-6">
                    + Enroll New Student
                </button>
                
                <div class="border-t border-slate-700 pt-6">
                    <h4 class="text-slate-300 font-bold mb-2">Directory Context</h4>
                    <p class="text-slate-500 text-sm italic">
                        Profiles synchronized seamlessly across reports, scoring grids, and monitoring streams.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>