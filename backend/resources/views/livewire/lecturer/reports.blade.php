<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Group;
use App\Models\Mark;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.app')] class extends Component {
    public $groupList = [];
    public ?int $selectedGroupId = null;

    // Top summary stats derived from real platform database metrics
    public array $performanceReports = [];

    // Detailed breakdown tracking student activities matrix from database
    public $studentPerformanceData = [];

    public function mount(): void
    {
        // Fetch groups from database for group-specific context & statistics[cite: 1, 2]
        $this->groupList = Group::all();
        if ($this->groupList->isNotEmpty()) {
            $this->selectedGroupId = $this->groupList->first()->id;
        }

        $this->loadAnalyticsData();
    }

    public function updatedSelectedGroupId(): void
    {
        $this->loadAnalyticsData();
    }

    public function loadAnalyticsData(): void
    {
        $group = Group::find($this->selectedGroupId);

        if (!$group) {
            $this->performanceReports = [
                ['metric' => 'Average Test Score', 'value' => '0%', 'trend' => 'No data', 'color' => 'text-[#24a065] dark:text-[#52c48a]'],
                ['metric' => 'Forum Response Rate', 'value' => '0%', 'trend' => 'No data', 'color' => 'text-blue-600'],
                ['metric' => 'At-Risk Student Alerts', 'value' => '00', 'trend' => '0 flagged', 'color' => 'text-amber-500'],
            ];
            $this->studentPerformanceData = collect();
            return;
        }

        // 1. Fetch group members
        $members = $group->members()->get();
        $memberIds = $members->pluck('id');

        // 2. Calculate average quiz score from marks table for this group's members
        $avgScore = Mark::whereIn('user_id', $memberIds)->avg('score') ?? 0;

        // 3. Calculate forum metrics (posts & replies counts from DB)
        $totalPosts = DB::table('topics')->where('group_id', $group->id)->count();
        $totalReplies = DB::table('posts')
            ->join('topics', 'posts.topic_id', '=', 'topics.id')
            ->where('topics.group_id', $group->id)
            ->count();

        // 4. Build student performance matrix dynamically using 'user_id' matching migration
        $this->studentPerformanceData = $members->map(function($student) use ($group) {
            // Get student quiz average
            $quizAvg = Mark::where('user_id', $student->id)->avg('score') ?? 0;
            
            // Get topics created by student in this group (using 'user_id')
            $postsCount = DB::table('topics')
                ->where('group_id', $group->id)
                ->where('user_id', $student->id) 
                ->count();

            // Get replies/posts made by student in this group's topics (using 'posts.user_id')
            $repliesCount = DB::table('posts')
                ->join('topics', 'posts.topic_id', '=', 'topics.id')
                ->where('topics.group_id', $group->id)
                ->where('posts.user_id', $student->id)
                ->count();

            // Determine standing based on performance
            $standing = 'Satisfactory';
            if ($quizAvg >= 80 && $repliesCount >= 10) {
                $standing = 'Excellent';
            } elseif ($quizAvg >= 70) {
                $standing = 'Good';
            } elseif ($quizAvg < 50) {
                $standing = 'At Risk';
            }

            return [
                'name' => $student->name,
                'id' => 'STU' . str_pad($student->id, 3, '0', STR_PAD_LEFT),
                'quiz_avg' => round($quizAvg) . '%',
                'posts' => $postsCount,
                'replies' => $repliesCount,
                'standing' => $standing
            ];
        });

        // Count actual at-risk students from collection
        $atRiskCount = $this->studentPerformanceData->where('standing', 'At Risk')->count();

        // Populate summary metric cards
        $this->performanceReports = [
            ['metric' => 'Average Test Score', 'value' => round($avgScore, 1) . '%', 'trend' => 'Group aggregate baseline', 'color' => 'text-[#24a065] dark:text-[#52c48a]'],
            ['metric' => 'Forum Response Rate', 'value' => ($totalPosts + $totalReplies) . ' Interactors', 'trend' => 'Active community footprint', 'color' => 'text-blue-600'],
            ['metric' => 'At-Risk Student Alerts', 'value' => str_pad($atRiskCount, 2, '0', STR_PAD_LEFT), 'trend' => 'Requiring intervention', 'color' => 'text-amber-500'],
        ];
    }
}; ?>

<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6 min-h-screen text-zinc-900 dark:text-zinc-100 transition-colors duration-200">
    
    <!-- Top Header & Group Selector Context Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-sm border-t-4 border-t-[#24a065]">
        <div>
            <h1 class="text-xl font-black tracking-tight text-zinc-900 dark:text-white">Academic Performance & Analytics Reports</h1>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">Review aggregated group analytics, classroom assessment metrics[cite: 1, 2], and student participation footprints.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4">
            <!-- Group Scope Selector -->
            <div class="w-full sm:w-56">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Target Study Group Scope</label>
                <select wire:model.live="selectedGroupId" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-[#24a065] focus:outline-none transition-all">
                    @foreach($groupList as $grp)
                        <option value="{{ $grp->id }}">{{ $grp->groupName ?? $grp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Export Toolkit Action Items -->
            <div class="shrink-0 flex gap-2 pt-4 sm:pt-0">
                <button onclick="window.print()" class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer border border-zinc-300 dark:border-zinc-700 flex items-center gap-2">
                    <span>🖨️</span> Print
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Status Bar Feedback -->
    @if (session()->has('status'))
        <div class="text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 p-3 rounded-xl border border-emerald-200 dark:border-emerald-800 shadow-sm">
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
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col gap-1 shadow-xs">
                        <span class="text-[10px] text-zinc-400 font-extrabold uppercase tracking-wider">{{ $card['metric'] }}</span>
                        <span class="text-xl font-black {{ $card['color'] }}">{{ $card['value'] }}</span>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-bold">{{ $card['trend'] }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Detailed Student Metric Log Table Ledger -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                    <h3 class="font-black text-xs uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Class Performance & Footprint Ledger</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-950/20 border-b border-zinc-200 dark:border-zinc-800 font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5">Student Particulars</th>
                                <th class="p-3.5 text-center">Quiz Average</th>
                                <th class="p-3.5 text-center">Topics Created</th>
                                <th class="p-3.5 text-center">Discussion Replies</th>
                                <th class="p-3.5 text-center">Standing Evaluation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-medium text-zinc-700 dark:text-zinc-300">
                            @forelse($studentPerformanceData as $data)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/20 transition-colors" wire:key="student-row-{{ $data['id'] }}">
                                <td class="p-3.5">
                                    <span class="block font-bold text-zinc-900 dark:text-white">{{ $data['name'] }}</span>
                                    <span class="block text-[10px] font-mono text-zinc-400 mt-0.5">{{ $data['id'] }}</span>
                                </td>
                                <td class="p-3.5 text-center font-mono font-bold text-zinc-800 dark:text-zinc-200">
                                    {{ $data['quiz_avg'] }}
                                </td>
                                <td class="p-3.5 text-center font-semibold text-zinc-500 dark:text-zinc-400">
                                    {{ $data['posts'] }}
                                </td>
                                <td class="p-3.5 text-center font-semibold text-zinc-500 dark:text-zinc-400">
                                    {{ $data['replies'] }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full whitespace-nowrap
                                        {{ $data['standing'] === 'Excellent' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : '' }}
                                        {{ $data['standing'] === 'Good' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400 border border-blue-200 dark:border-blue-800' : '' }}
                                        {{ $data['standing'] === 'Satisfactory' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : '' }}
                                        {{ $data['standing'] === 'At Risk' ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400 border border-red-200 dark:border-red-800 font-black animate-pulse' : '' }}
                                    ">
                                        {{ $data['standing'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-zinc-400 italic">
                                    No student enrollment or performance records found for this active group context.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- RIGHT PANEL: Solid Emerald Strategy Evaluation Insights (4 out of 12 columns) -->
        <div class="xl:col-span-4 p-5 bg-[#52c48a] rounded-2xl flex flex-col gap-4 text-zinc-900 shadow-sm min-h-[380px]">
            <div class="border-b border-zinc-950/10 pb-2 text-center">
                <span class="inline-block px-2 py-0.5 bg-zinc-950 text-white text-[9px] font-black uppercase rounded mb-1">AI Analytics Engine</span>
                <h3 class="font-black text-sm uppercase tracking-wider text-zinc-950">Participation Analysis</h3>
                <p class="text-[11px] text-zinc-900 font-semibold mt-0.5">Automated instructional diagnostic insights</p>
            </div>

            <!-- Insights Analysis Narrative Container -->
            <div class="flex flex-col gap-3 font-semibold text-xs text-zinc-800 leading-relaxed grow">
                <div class="bg-white/95 dark:bg-zinc-900/95 dark:text-zinc-100 p-4 rounded-xl shadow-xs flex flex-col gap-1 border border-white/20">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-700 dark:text-emerald-400">Class Insight</span>
                    <p class="font-medium text-zinc-700 dark:text-zinc-300">
                        Discussion footprint rates and quiz averages are dynamically computed per selected study group context[cite: 1, 2].
                    </p>
                </div>

                <div class="bg-white/95 dark:bg-zinc-900/95 dark:text-zinc-100 p-4 rounded-xl shadow-xs flex flex-col gap-1 border border-white/20">
                    <span class="text-[10px] font-extrabold uppercase text-amber-700 dark:text-amber-400">Action Required</span>
                    <p class="font-medium text-zinc-700 dark:text-zinc-300">
                        Students falling below baseline engagement criteria are flagged as <strong>At Risk</strong>. Direct warning notices can be issued via the moderation gateway.
                    </p>
                </div>
            </div>

            <div class="text-[10px] text-zinc-950 font-bold opacity-90 text-center mt-auto leading-relaxed bg-white/30 p-2.5 rounded-lg border border-white/20">
                📊 Data sets sync dynamically with active database records and group parameters.
            </div>
        </div>

    </div>
</div>