<?php
use Livewire\Volt\Component;
use App\Models\Group;

new class extends Component {
    public $groups = [];
    public $selectedGroupId = null;
    
    // UI State for Group Statistics[cite: 2]
    public $totalPosts = 0;
    public $activeMembers = 0;
    public $totalMembers = 0;
    public $quizParticipationRate = 0;
    public $topContributor = 'N/A';
    public $students = []; 

    public function mount()
    {
        $this->groups = Group::all(); 
        
        if ($this->groups->isNotEmpty()) {
            $this->selectedGroupId = $this->groups->first()->id;
            $this->loadGroupStatistics();
        } else {
            // Fallback dummy data for UI testing if the database is empty
            $this->setupDummyData();
        }
    }

    // Livewire Lifecycle Hook: Runs automatically when $selectedGroupId changes
    public function updatedSelectedGroupId()
    {
        $this->loadGroupStatistics();
    }

    public function loadGroupStatistics()
    {
        $group = Group::with(['members', 'topics.posts'])->find($this->selectedGroupId);
        
        if ($group) {
            $this->totalPosts = $group->topics->sum('postCount');
            $this->totalMembers = $group->members->count();
            $this->activeMembers = $group->members->where('status', 'ACTIVE')->count();
            
            // These would be replaced by your actual IAnalyticsEngine computations[cite: 2]
            $this->quizParticipationRate = 85; 
            $this->topContributor = 'Patience R.'; 
            $this->students = $group->members; 
        }

        // Dispatch new chart data to the frontend (Web vs Desktop interfaces[cite: 1])
        $this->dispatch('update-chart-data', data: [
            'web' => [120, 210, 150, 310, 220, 400, rand(200, 450)],
            'desktop' => [80, 120, 100, 180, 150, 250, rand(100, 300)] 
        ]);
    }

    private function setupDummyData()
    {
        // Fallback for visual rendering
        $this->groups = [
            (object)['id' => 1, 'groupName' => 'Software Engineering Group'],
            (object)['id' => 2, 'groupName' => 'Database Admin Group']
        ];
        $this->selectedGroupId = 1;
        $this->totalPosts = 1245;
        $this->activeMembers = 42;
        $this->totalMembers = 45;
        $this->quizParticipationRate = 85;
        $this->topContributor = 'Patience R.';
        
        $this->students = [
            (object)['name' => 'Elizeous Anthony', 'last_seen' => '2 hours ago', 'warnings' => 0, 'status' => 'ACTIVE'],
            (object)['name' => 'John Doe', 'last_seen' => '14 days ago', 'warnings' => 1, 'status' => 'WARNING SENT'],
            (object)['name' => 'Mark Taylor', 'last_seen' => '45 days ago', 'warnings' => 2, 'status' => 'BLACKLISTED'],
        ];
    }
}; ?>

<<<<<<< HEAD
<div class="p-4 sm:p-6 space-y-8 bg-slate-900 min-h-screen text-slate-50">
    
    <!-- Page Header & Group Selector -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h2 class="text-xl sm:text-2xl font-bold tracking-wide">Dashboard & Analytics</h2>
        <select wire:model.live="selectedGroupId" class="w-full sm:w-auto bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
=======
<div class="p-6 space-y-8 bg-slate-900 min-h-screen text-slate-50">
    
    <!-- Page Header & Group Selector -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold tracking-wide">Dashboard & Analytics</h2>
        <select wire:model.live="selectedGroupId" class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
>>>>>>> 655dacc40daca71479b014ca04523b096e7faf09
            @foreach($groups as $group)
                <option value="{{ $group->id }}">{{ $group->groupName }}</option>
            @endforeach
        </select>
    </div>

    <!-- 1. GROUP STATISTICS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-sm">
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Total Posts</p>
            <p class="text-3xl font-bold text-white mt-2">{{ number_format($totalPosts) }}</p>
            <p class="text-xs text-green-400 mt-1">Live tracking</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-sm">
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Active Members</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $activeMembers }} / {{ $totalMembers }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $totalMembers - $activeMembers }} members inactive</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-sm">
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Quiz Participation</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $quizParticipationRate }}%</p>
            <p class="text-xs text-slate-500 mt-1">Average completion rate</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-sm">
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Top Contributor</p>
            <p class="text-2xl font-bold text-green-400 mt-2 truncate">{{ $topContributor }}</p>
            <p class="text-xs text-slate-500 mt-1">Highest engagement</p>
        </div>
    </div>

    <!-- 2. FULL-WIDTH ACTIVITY GRAPH -->
    <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-sm w-full flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-slate-300 font-semibold uppercase tracking-wider text-sm">7-Day Access Activity (Web vs Desktop)</h3>
            <button class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-300 px-3 py-1.5 rounded transition border border-slate-600">Export to PDF</button>
        </div>
        <div class="relative w-full h-[350px]">
            <canvas id="activityChart" wire:ignore></canvas>
        </div>
    </div>

    <!-- 3. STUDENT ACTIVENESS & WARNING TABLE -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center">
            <h3 class="text-slate-300 font-semibold uppercase tracking-wider text-sm">Student Activeness Monitor</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-700 bg-slate-800/50 text-xs uppercase tracking-wider text-slate-400">
                        <th class="p-4 font-medium">Member Name</th>
                        <th class="p-4 font-medium">Last Seen</th>
                        <th class="p-4 font-medium">Warnings Issued</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-700/50">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-700/20 transition-colors">
                            <td class="p-4 font-medium text-slate-200">{{ $student->name }}</td>
                            <td class="p-4 text-slate-400">{{ $student->last_seen ?? 'Unknown' }}</td>
                            
                            <!-- Dynamic Warning Colors -->
                            <td class="p-4 font-medium 
                                {{ $student->warnings == 0 ? 'text-slate-400' : '' }}
                                {{ $student->warnings == 1 ? 'text-orange-400' : '' }}
                                {{ $student->warnings >= 2 ? 'text-red-400' : '' }}">
                                {{ $student->warnings }} / 2
                            </td>
                            
                            <!-- Dynamic Status Badges -->
                            <td class="p-4">
                                @if($student->status === 'ACTIVE')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/30 text-green-400 border border-green-800/50">
                                        Active
                                    </span>
                                @elseif($student->status === 'WARNING SENT')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-900/30 text-orange-400 border border-orange-800/50">
                                        Warning Sent
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/30 text-red-400 border border-red-800/50">
                                        Blacklisted
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Dynamic Actions -->
                            <td class="p-4 text-right">
                                @if($student->status === 'ACTIVE')
                                    <button class="text-slate-400 hover:text-white transition">•••</button>
                                @elseif($student->status === 'WARNING SENT')
                                    <button class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-1.5 rounded transition">Issue Warning</button>
                                @else
                                    <button class="text-xs bg-slate-700 hover:bg-slate-600 text-green-400 px-3 py-1.5 rounded transition">Lift Ban</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-500 italic">No members found in this group.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.color = '#94a3b8'; 
            Chart.defaults.borderColor = '#334155'; 

            const activityCtx = document.getElementById('activityChart').getContext('2d');

            const gradientBlue = activityCtx.createLinearGradient(0, 0, 0, 400);
            gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); 
            gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); 

            const gradientTeal = activityCtx.createLinearGradient(0, 0, 0, 400);
            gradientTeal.addColorStop(0, 'rgba(45, 212, 191, 0.5)'); 
            gradientTeal.addColorStop(1, 'rgba(45, 212, 191, 0.0)'); 

            let accessChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                    datasets: [
                        {
                            label: 'Web Users',
                            data: [120, 210, 150, 310, 220, 400, 280],
                            borderColor: '#3b82f6', 
                            backgroundColor: gradientBlue,
                            borderWidth: 2, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 6 
                        },
                        {
                            label: 'Desktop Users',
                            data: [80, 120, 100, 180, 150, 250, 190],
                            borderColor: '#2dd4bf', 
                            backgroundColor: gradientTeal,
                            borderWidth: 2, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { 
                        legend: { 
                            display: true, position: 'bottom', 
                            labels: { usePointStyle: true, boxWidth: 8, padding: 20 }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b', titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1', borderColor: '#334155', borderWidth: 1
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: '#334155' }, border: { display: false } }
                    }
                }
            });

            // Listen for Livewire updates to dynamically change chart data
            window.addEventListener('update-chart-data', event => {
                const newData = event.detail[0].data;
                accessChart.data.datasets[0].data = newData.web;
                accessChart.data.datasets[1].data = newData.desktop;
                accessChart.update();
            });
        });
    </script>
@endpush