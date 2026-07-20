<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Group;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.app')] class extends Component {
    public $activeTopics = [];
    public $pendingFlags = [];
    public $groupList = [];
    
    public int $flagCount = 0;
    public string $newChannelCode = '';
    public string $newChannelTitle = '';
    public ?int $selectedGroupId = null;

    // Management Modal State
    public bool $showManageModal = false;
    public ?array $activeManageTopic = null;

    // Broadcast Notice state
    public string $broadcastNotice = '';

    public function mount(): void
    {
        $this->groupList = Group::all();
        if ($this->groupList->isNotEmpty()) {
            $this->selectedGroupId = $this->groupList->first()->id;
        }

        $this->loadTopics();
        $this->loadFlags();
    }

    public function loadTopics(): void
    {
        $query = Topic::query();
        if ($this->selectedGroupId) {
            $query->where('group_id', $this->selectedGroupId);
        }
        
        $this->activeTopics = $query->withCount('posts')->latest()->get()->map(function($topic) {
            return [
                'id' => $topic->id,
                'title' => $topic->description, 
                'replies' => $topic->posts_count ?? 0,
                'status' => 'Active',
                'code' => 'CS_' . str_pad($topic->id, 4, '0', STR_PAD_LEFT),
                'time' => $topic->created_at ? $topic->created_at->diffForHumans() : 'Just now'
            ];
        })->toArray();
    }

    public function loadFlags(): void
    {
        $flags = DB::table('blacklists')->where('status', 'ACTIVE')->get();
        
        $this->pendingFlags = $flags->map(function($f){
            return [
                'id' => $f->id,
                'user' => 'User #' . $f->user_id,
                'reason' => 'Inactivity / Warning count: ' . $f->warning_count,
                'preview' => 'Flagged by automated Blacklist Engine security filter.'
            ];
        })->toArray();

        $this->flagCount = count($this->pendingFlags);
    }

    public function updatedSelectedGroupId(): void
    {
        $this->loadTopics();
    }

    public function createChannel(): void
    {
        $this->validate([
            'newChannelCode' => 'required|string|max:10',
            'newChannelTitle' => 'required|string|max:255',
            'selectedGroupId' => 'required|exists:groups,id'
        ]);

        Topic::create([
            'group_id' => $this->selectedGroupId,
            'description' => $this->newChannelTitle,
            'user_id' => Auth::id() ?? 1,
            'post_count' => 0
        ]);

        $this->newChannelCode = '';
        $this->newChannelTitle = '';

        $this->loadTopics();
        $this->dispatch('channel-created');
        session()->flash('status', 'Discussion channel created successfully.');
    }

    // Opens the interactive Manage modal for a specific channel
    public function openManageModal($topicId): void
    {
        $topic = Topic::find($topicId);
        if ($topic) {
            $this->activeManageTopic = [
                'id' => $topic->id,
                'title' => $topic->description,
                'code' => 'CS_' . str_pad($topic->id, 4, '0', STR_PAD_LEFT),
                'posts_count' => $topic->posts()->count(),
                'created_at' => $topic->created_at->toFormattedDateString()
            ];
            $this->showManageModal = true;
        }
    }

    public function deleteChannel($topicId): void
    {
        Topic::where('id', $topicId)->delete();
        $this->showManageModal = false;
        $this->loadTopics();
        session()->flash('status', 'Discussion channel deleted successfully.');
    }

    public function publishBroadcast(): void
    {
        $this->validate([
            'broadcastNotice' => 'required|string|max:500'
        ]);

        $userList = DB::table('users')->pluck('id');
        foreach($userList as $uid){
            DB::table('notifications')->insert([
                'user_id' => $uid,
                'message' => 'ADMIN NOTICE: ' . $this->broadcastNotice,
                'sent_at' => now(),
                'read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->broadcastNotice = '';
        session()->flash('broadcast_status', 'System advisory successfully deployed to all active room streams.');
    }

    public function dismissFlag($flagId): void
    {
        DB::table('blacklists')->where('id', $flagId)->update(['status' => 'DISMISSED']);
        $this->loadFlags();
    }

    public function applyBan($flagId): void
    {
        DB::table('blacklists')->where('id', $flagId)->update(['status' => 'SUSPENDED']);
        $this->loadFlags();
    }
}; ?>

<div x-data="{ showCreateModal: false }" @channel-created.window="showCreateModal = false" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 bg-white dark:bg-zinc-950 min-h-screen text-zinc-900 dark:text-zinc-100 transition-colors duration-200">
    
    <!-- Top Toolbar with Brand Emerald Accents -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-sm border-t-4 border-t-[#24a065]">
        <div>
            <h1 class="text-xl font-black tracking-tight text-zinc-900 dark:text-white">Forum & Moderation Control Center</h1>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">Manage study group streams, evaluate communication channels, and process policy infractions.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 px-3 py-1.5 rounded-xl text-center">
                    <span class="block text-sm font-black text-[#24a065] dark:text-[#52c48a]">{{ count($activeTopics) }}</span>
                    <span class="block text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Channels</span>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 px-3 py-1.5 rounded-xl text-center">
                    <span class="block text-sm font-black text-amber-600 dark:text-amber-400">{{ $flagCount }}</span>
                    <span class="block text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Flags</span>
                </div>
            </div>

            <div class="w-full sm:w-56">
                <select wire:model.live="selectedGroupId" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-[#24a065] focus:outline-none transition-all">
                    @foreach($groupList as $grp)
                        <option value="{{ $grp->id }}">{{ $grp->groupName ?? $grp->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('status'))
        <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session()->has('broadcast_status'))
        <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span>{{ session('broadcast_status') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        
        <!-- SECTION 1: Active Discussion Forums -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-zinc-900 dark:text-white">Active Discussion Forums</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Manage conversation threads, review interaction rates, and export transcripts[cite: 1, 2].</p>
                </div>
                <button type="button" @click="showCreateModal = true" class="bg-[#24a065] hover:bg-[#1c7d4e] text-white font-bold text-xs px-4 py-2 rounded-xl shadow transition-colors cursor-pointer border-none flex items-center justify-center gap-1.5">
                    <span>+ Create Channel</span>
                </button>
            </div>

            <!-- Channel Items List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[320px] overflow-y-auto pr-1">
                @forelse($activeTopics as $topic)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-4" wire:key="topic-{{ $topic['id'] }}">
                        <div class="truncate">
                            <span class="text-[9px] font-black text-[#24a065] dark:text-[#52c48a] uppercase tracking-wider block mb-0.5">{{ $topic['code'] }}</span>
                            <h3 class="text-xs font-bold text-zinc-900 dark:text-white truncate">{{ $topic['title'] }}</h3>
                            <span class="text-[10px] text-zinc-400 font-medium mt-1 block">{{ $topic['replies'] }} interactions • Last post {{ $topic['time'] }}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">

                            
                            <!-- Functional Manage Button -->
                            <button type="button" wire:click="openManageModal({{ $topic['id'] }})" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white text-[11px] font-bold px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs cursor-pointer transition-colors">Manage</button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 text-xs text-zinc-400 italic">No active discussion channels found for this group scope.</div>
                @endforelse
            </div>

            <!-- Global Forum Advisory Notice Console -->
            <div class="bg-zinc-50 dark:bg-zinc-950 rounded-xl p-4 border border-zinc-200 dark:border-zinc-800 mt-4">
                <label for="notice" class="text-[10px] font-black text-zinc-500 uppercase tracking-wider block mb-1.5">Broadcast System Advisory Notice</label>
                <textarea id="notice" wire:model="broadcastNotice" placeholder="Publish an administrative statement directly to the top banners of all active room streams[cite: 1, 2]..." class="w-full bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 h-16 focus:outline-none focus:ring-2 focus:ring-[#24a065] shadow-inner"></textarea>
                @error('broadcastNotice') <span class="text-[10px] text-red-500 font-bold">{{ $message }}</span> @enderror
                <div class="mt-2 flex justify-end">
                    <button type="button" wire:click="publishBroadcast" class="bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-bold text-[10px] tracking-wide px-4 py-2 rounded-lg shadow transition-all cursor-pointer border-none">Publish Notification</button>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Gateway Rules Monitoring & Moderation Queue -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-2xl shadow-sm space-y-4">
            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-zinc-900 dark:text-white">Gateway Rules Monitoring & Moderation Queue</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Review automated infraction flags, issue formal warnings[cite: 1, 2], and process account suspensions.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[320px] overflow-y-auto pr-1">
                @forelse($pendingFlags as $flag)
                    <div class="bg-zinc-50 dark:bg-zinc-950 rounded-xl p-4 border border-zinc-200 dark:border-zinc-800 shadow-xs flex flex-col justify-between gap-3" wire:key="flag-{{ $flag['id'] }}">
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-[10px] font-bold">
                                <span class="text-zinc-500 dark:text-zinc-400">User: <strong class="text-zinc-900 dark:text-white">{{ $flag['user'] }}</strong></span>
                                <span class="text-red-600 dark:text-red-400 font-extrabold uppercase tracking-tight bg-red-50 dark:bg-red-950/50 px-1.5 py-0.5 rounded border border-red-200 dark:border-red-900">Flagged</span>
                            </div>
                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 leading-tight">{{ $flag['reason'] }}</p>
                            <span class="block text-[11px] italic bg-white dark:bg-zinc-900 text-zinc-500 p-2 rounded border-l-2 border-amber-500 truncate">"{{ $flag['preview'] }}"</span>
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" wire:click="dismissFlag({{ $flag['id'] }})" class="bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold text-[10px] px-3 py-1.5 rounded-lg cursor-pointer border-none shadow-xs">Dismiss</button>
                            <button type="button" wire:click="applyBan({{ $flag['id'] }})" class="bg-red-600 hover:bg-red-700 text-white font-bold text-[10px] px-3 py-1.5 rounded-lg cursor-pointer border-none shadow-xs">Apply Ban</button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-xs text-zinc-400 italic">No active rule flags or warnings currently pending administrative review.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- POPUP MODAL WIREFRAME COMPONENT LAYER FOR CHANNEL CREATION -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" style="display: none;">
        <div @click.outside="showCreateModal = false" class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 flex flex-col gap-4">
            <div class="flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h3 class="text-sm font-black uppercase text-zinc-900 dark:text-white">Create New Discussion Channel</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 font-bold text-base cursor-pointer">&times;</button>
            </div>

            <form wire:submit.prevent="createChannel" class="flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Target Group</label>
                    <select wire:model="selectedGroupId" class="w-full bg-zinc-50 dark:bg-zinc-950 text-xs border border-zinc-300 dark:border-zinc-700 rounded-lg p-2.5 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-[#24a065] focus:outline-none" required>
                        @foreach($groupList as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->groupName ?? $grp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Channel / Course Code</label>
                    <input type="text" wire:model="newChannelCode" placeholder="e.g., CS_4103" class="w-full bg-zinc-50 dark:bg-zinc-950 text-xs border border-zinc-300 dark:border-zinc-700 rounded-lg p-2.5 text-zinc-800 dark:text-zinc-200 uppercase focus:ring-2 focus:ring-[#24a065] focus:outline-none" required />
                    @error('newChannelCode') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Discussion Topic Title</label>
                    <input type="text" wire:model="newChannelTitle" placeholder="e.g., Distributed Database Consensus" class="w-full bg-zinc-50 dark:bg-zinc-950 text-xs border border-zinc-300 dark:border-zinc-700 rounded-lg p-2.5 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-[#24a065] focus:outline-none" required />
                    @error('newChannelTitle') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-bold rounded-xl cursor-pointer border-none transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#24a065] hover:bg-[#1c7d4e] text-white text-xs font-bold rounded-xl shadow cursor-pointer border-none transition-colors">Save & Publish</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MANAGE CHANNEL MODAL LAYER -->
    @if($showManageModal && $activeManageTopic)
    <div class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 flex flex-col gap-4">
            <div class="flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h3 class="text-sm font-black uppercase text-zinc-900 dark:text-white">Manage Channel: {{ $activeManageTopic['code'] }}</h3>
                <button wire:click="$set('showManageModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 font-bold text-base cursor-pointer">&times;</button>
            </div>

            <div class="space-y-3 text-xs text-zinc-700 dark:text-zinc-300">
                <p><strong>Topic Title:</strong> {{ $activeManageTopic['title'] }}</p>
                <p><strong>Total Message Interactions:</strong> {{ $activeManageTopic['posts_count'] }}</p>
                <p><strong>Date Established:</strong> {{ $activeManageTopic['created_at'] }}</p>
            </div>

            <div class="flex justify-between items-center gap-2 mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" wire:click="deleteChannel({{ $activeManageTopic['id'] }}" wire:confirm="Are you sure you want to delete this discussion channel?" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl cursor-pointer border-none transition-colors">Delete Channel</button>
                <button type="button" wire:click="$set('showManageModal', false)" class="px-5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl cursor-pointer border-none transition-colors">Close</button>
            </div>
        </div>
    </div>
    @endif
</div>