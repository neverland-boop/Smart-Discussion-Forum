<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On; 
use App\Services\PostService;
use App\Services\TopicService;
use App\Services\GroupService;
use App\Services\ModerationService;
use App\Models\Group;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $expandedGroup = null; 
    public $activeTopic = null; 
    public $activeGroup = null; 
    public $showTopicModal = false;
    public $selectedGroupId = null;
    
    public $newTopicTitle = '';
    public $newTopicDescription = '';
    public $isPrivate = false; 

    #[Validate('required|string|max:2000')]
    public $newMessage = '';
    
    public bool $showLeftSidebar = true;
    public bool $showCreateModal = false;
    public bool $showJoinModal = false;

    public function openAddTopicModal($groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->showTopicModal = true;
    }

    public function saveTopic(TopicService $topicService)
    {
        $validated = $this->validate([
            'newTopicTitle' => 'required|string|min:3|max:255',
            'newTopicDescription' => 'nullable|string|max:1000',
            'isPrivate' => 'boolean', 
        ]);

        $topicService->createTopic([
            'title' => $this->newTopicTitle,
            'description' => $this->newTopicDescription,
            'group_id' => $this->selectedGroupId,
            'is_private' => $this->isPrivate,
        ], auth()->user());

        $this->reset(['showTopicModal', 'newTopicTitle', 'newTopicDescription', 'isPrivate', 'selectedGroupId']);
    }

    public function requestAccess(TopicService $topicService, $topicId)
    {
        $topicService->requestAccess($topicId, auth()->user());
    }

    public function approveParticipant(TopicService $topicService, $topicId, $userId)
    {
        $topicService->approveParticipant($topicId, $userId, auth()->user());
    }

    public function sendMessage(PostService $postService, ModerationService $moderationService)
    {
        if (!$this->activeTopic) return;
        $this->validate(); 

        // Save Message
        $postService->createPost(['content' => $this->newMessage], $this->activeTopic);

        // Clear Blacklist Warnings if compliant
        $moderationService->clearWarningsIfCompliant(auth()->user());

        $this->newMessage = '';
        $this->dispatch('message-sent'); 
    }

    public function warnParticipant(ModerationService $moderationService, $topicId, $userId)
    {
        $moderationService->warnParticipant($topicId, $userId, auth()->user());
    }

    public function getListeners()
    {
        if (!$this->activeTopic) return [];
        return [
            "echo-private:topic.{$this->activeTopic},MessageSent" => 'render'
        ];
    }
    
    public function refreshMessages() {}

    public function with(PostService $postService, GroupService $groupService): array
    {
        $formattedMessages = collect();
        $topicModel = null;
        $topicMembers = collect();
        $pendingRequests = collect();
        
        if ($this->activeTopic) {
            $topicModel = Topic::find($this->activeTopic);
            $posts = $postService->getPostsByTopic($this->activeTopic);
            
            $formattedMessages = $posts->map(function($post) {
                return [
                    'id'      => $post->id,
                    'sender'  => $post->author->name ?? 'Unknown User', 
                    'text'    => $post->content,
                    'time'    => $post->created_at->format('g:i A'),
                    'is_mine' => $post->user_id === auth()->id(),
                ];
            });

            if ($topicModel->is_private) {
                $topicMembers = $topicModel->participants()->wherePivot('status', 'approved')->get();
                
                if ($topicModel->user_id === auth()->id() || auth()->user()->hasRole('admin')) {
                    $pendingRequests = $topicModel->participants()->wherePivot('status', 'pending')->get();
                }
            } else {
                $topicMembers = $this->activeGroup ? $this->activeGroup->members()->get() : collect();
            }
        }

        return [
            'messages'        => $formattedMessages,
            'topicModel'      => $topicModel, 
            'groups'          => $groupService->getUserGroups(auth()->user()),
            'availableGroups' => $groupService->getAvailableGroups(auth()->user()),
            'activeMembers'   => $topicMembers,
            'pendingRequests' => $pendingRequests,
        ];
    }

    public function toggleGroup($groupId)
    {
        $this->expandedGroup = $this->expandedGroup === $groupId ? null : $groupId;
    }

    public function joinGroup(GroupService $groupService, $groupId)
    {
        $groupService->joinGroup($groupId, auth()->user());
        $this->showJoinModal = false;
        $this->dispatch('group-joined'); 
    }

    public function selectTopic($topicId)
    {
        $this->activeTopic = $topicId;
        $topic = Topic::find($topicId);
        $this->activeGroup = $topic ? $topic->group : null;
    }

    #[On('group-created')]
    public function updateSidebar() {}
}; ?>


<div class="h-full w-full bg-slate-900 flex overflow-hidden">
    
    <!-- LEFT PANEL: Groups & Topics -->
    <div class="bg-slate-800 border-r border-slate-700 flex flex-col flex-shrink-0 transition-all duration-300 {{ $showLeftSidebar ? 'w-80' : 'w-16' }}">
        
        <div class="h-16 p-4 border-b border-slate-700 bg-slate-800 flex items-center {{ $showLeftSidebar ? 'justify-between' : 'justify-center' }}">
            @if($showLeftSidebar)
                <h2 class="text-lg font-bold text-white truncate">Your Groups</h2>
                <div class="flex items-center gap-1">
                    <x-modal-trigger>
                        <button class="p-1.5 bg-green-600/20 text-green-400 hover:bg-green-600 hover:text-white rounded-md transition" title="Create or Join Group">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </x-modal-trigger>
                    <button wire:click="$toggle('showLeftSidebar')" class="text-slate-400 hover:text-white p-1.5 rounded-md hover:bg-slate-700 transition" title="Toggle Sidebar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    </button>
                </div>
            @else
                <button wire:click="$toggle('showLeftSidebar')" class="text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-700 transition" title="Expand Sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </button>
            @endif
        </div>
        
        <div class="flex-1 overflow-y-auto">
            @if($showLeftSidebar)
                <div class="p-4 pt-2">
                    <input type="text" placeholder="Search topics..." class="w-full bg-slate-900 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-400 focus:outline-none focus:border-green-500 mb-2 shadow-inner">
                </div>
                
                <div class="px-2 space-y-1 pb-4">
                    @forelse($groups as $group)
                        <div>
                            <button wire:click="toggleGroup({{ $group->id }})" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-slate-700 transition">
                                <span class="font-semibold text-slate-200">{{ $group->name }}</span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform {{ $expandedGroup === $group->id ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            @if($expandedGroup === $group->id)
                                <div class="mt-1 mb-2 ml-4 space-y-1 border-l-2 border-slate-700 pl-2">
                                    @foreach($group->topics as $topic) 
                                        <button wire:click="selectTopic({{ $topic->id }})" class="w-full flex items-center justify-between p-2 rounded-lg text-sm transition {{ $activeTopic == $topic->id ? 'bg-green-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                                            <span class="truncate pr-2">
                                                @if($topic->is_private) 🔒 @endif # {{ $topic->title }}
                                            </span>
                                        </button>
                                    @endforeach
                                    
                                    <button wire:click="openAddTopicModal({{ $group->id }})" class="w-full flex items-center gap-2 p-2 rounded-lg text-sm text-slate-500 hover:text-green-400 hover:bg-slate-700 transition mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Add Topic
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-sm text-slate-500">You haven't joined any groups yet.</p>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="flex flex-col items-center py-4 space-y-4">
                    @foreach($groups as $group)
                        <button wire:click="$set('showLeftSidebar', true); toggleGroup({{ $group->id }})" class="w-10 h-10 rounded-xl bg-slate-700 hover:bg-green-600 flex items-center justify-center text-sm font-bold text-white transition shadow-md group relative" title="{{ $group->name }}">
                            {{ substr($group->name, 0, 1) }}
                        </button>
                    @endforeach
                    <button wire:click="$set('showLeftSidebar', true)" class="w-10 h-10 rounded-xl border border-dashed border-slate-600 text-slate-400 hover:border-green-500 hover:text-green-400 flex items-center justify-center transition" title="Add Group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- CENTER PANEL -->
    <div class="flex-1 flex flex-col bg-slate-900 relative min-w-0" x-data="{ showMembers: false }">
        
        @if($activeTopic && $topicModel)
            @php
                $participant = $topicModel->participants()->where('user_id', auth()->id())->first();
                $isAuthorized = !$topicModel->is_private || ($participant && $participant->pivot->status === 'approved');
            @endphp

            @if($isAuthorized)
                <!-- ACTIVE CHAT INTERFACE -->
                <div class="h-16 border-b border-slate-700 bg-slate-800 flex items-center px-6 flex-shrink-0 justify-between">
                    <div>
                        <h3 class="text-white font-bold text-lg leading-tight flex items-center gap-2">
                            <span class="text-slate-500">@if($topicModel->is_private) 🔒 @else # @endif</span> {{ $topicModel->title }}
                        </h3>
                        <p class="text-xs text-slate-400">{{ $activeGroup ? $activeGroup->name : 'Group Name' }}</p>
                    </div>

                    <button @click="showMembers = true" class="flex items-center gap-2 text-slate-400 hover:text-white transition px-3 py-1.5 rounded-md hover:bg-slate-700 shadow-sm border border-transparent hover:border-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="text-sm font-medium hidden sm:block">Members</span>
                    </button>
                </div>

<!-- Messages Area -->
<div wire:poll.2s.visible="refreshMessages" 
     x-data="{ scroll() { $el.scrollTop = $el.scrollHeight; } }" 
     x-init="scroll()" 
     x-on:message-sent.window="setTimeout(scroll, 100)" 
     class="flex-1 overflow-y-auto p-6 space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-700 hover:[&::-webkit-scrollbar-thumb]:bg-slate-600 [&::-webkit-scrollbar-thumb]:rounded-full">
    
    @forelse($messages as $message)
        <!-- Alpine Component for Sharing Logic & Hover State -->
        <div wire:key="msg-{{ $message['id'] }}" 
             x-data="{
                 shareText: '{{ addslashes($message['text']) }}',
                 shareUrl: window.location.href,
                 triggerShare() {
                     if (navigator.share) {
                         // Native mobile/macOS share sheet
                         navigator.share({
                             title: 'Forwarded Message',
                             text: this.shareText,
                             url: this.shareUrl
                         }).catch(console.error);
                     } else {
                         // Professional Desktop Fallback (Twitter/X Window)
                         window.open(
                             'https://twitter.com/intent/tweet?text=' + encodeURIComponent('Check out this post: ' + this.shareText) + '&url=' + encodeURIComponent(this.shareUrl),
                             '_blank',
                             'width=600,height=400,scrollbars=yes'
                         );
                     }
                 }
             }"
             class="group flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
            
            <div class="max-w-[90%] lg:max-w-[75%] flex flex-col {{ $message['is_mine'] ? 'items-end' : 'items-start' }}">
                
                <!-- Sender Name -->
                @if(!$message['is_mine'])
                    <span class="text-xs text-slate-400 ml-1 mb-1 block">{{ $message['sender'] }}</span>
                @else
                    <span class="text-xs text-slate-400 mr-1 mb-1 block text-right">You</span>
                @endif

                <!-- Bubble & Action Menu Wrapper -->
                <div class="flex items-center gap-2 {{ $message['is_mine'] ? 'flex-row-reverse' : 'flex-row' }}">
                    
                    <!-- Message Bubble -->
                    <div class="p-3 rounded-2xl shadow-sm {{ $message['is_mine'] ? 'bg-green-600 text-white rounded-tr-none' : 'bg-slate-800 text-slate-200 border border-slate-700 rounded-tl-none' }}">
                        {{ $message['text'] }}
                    </div>

                    <!-- Forward/Share Button (Hidden until hover/tap) -->
                    <button type="button" 
                            @click="triggerShare()"
                            title="Forward Message"
                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 rounded-full hover:bg-slate-700 text-slate-400 hover:text-blue-400 focus:outline-none focus:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316M15 12a3 3 0 100-6 3 3 0 000 6zm0 0a3 3 0 100 6 3 3 0 000-6z"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Timestamp -->
                <span class="text-[10px] text-slate-500 mt-1 block {{ $message['is_mine'] ? 'mr-1' : 'ml-1' }}">{{ $message['time'] }}</span>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center h-full text-slate-500">
            <svg class="w-12 h-12 mb-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            <p>No messages yet. Start the conversation!</p>
        </div>
    @endforelse
</div>

                <!-- SLIDE-OVER DRAWER: Active Members & Pending Requests -->
                <div x-show="showMembers" x-transition.opacity @click="showMembers = false" class="absolute inset-0 bg-slate-900/60 z-40" style="display: none;"></div>
                <div x-show="showMembers" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="absolute inset-y-0 right-0 w-72 bg-slate-800 border-l border-slate-700 shadow-2xl flex flex-col z-50" style="display: none;">
                    <div class="h-16 p-4 border-b border-slate-700 flex items-center justify-between bg-slate-800/95 backdrop-blur">
                        <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">Group Members</h2>
                        <button @click="showMembers = false" class="text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-4 overflow-y-auto flex-1 space-y-6">
                        
                        <!-- SECTION 1: PENDING REQUESTS -->
                        @if($pendingRequests && $pendingRequests->count() > 0)
                            <div>
                                <h3 class="text-[10px] font-bold text-orange-400 uppercase tracking-wider mb-3 border-b border-slate-700 pb-2">
                                    Pending Approvals ({{ $pendingRequests->count() }})
                                </h3>
                                <div class="space-y-2">
                                    @foreach($pendingRequests as $applicant)
                                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900 border border-slate-700 shadow-sm">
                                            <div class="flex items-center gap-2 overflow-hidden">
                                                <div class="shrink-0 h-8 w-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-300">
                                                    {{ strtoupper(substr($applicant->name, 0, 1)) }}
                                                </div>
                                                <span class="text-sm font-medium text-slate-200 truncate">{{ $applicant->name }}</span>
                                            </div>
                                            <button wire:click="approveParticipant({{ $topicModel->id }}, {{ $applicant->id }})" class="shrink-0 text-xs bg-green-600 hover:bg-green-500 text-white px-3 py-1.5 rounded-md transition font-medium">
                                                Approve
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- SECTION 2: APPROVED MEMBERS -->
                        <div>
                            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-700 pb-2">
                                @if($topicModel && $topicModel->is_private) Approved Participants @else Group Members @endif
                            </h3>
                            <div class="space-y-1">
                                @forelse($activeMembers as $member)
                                    <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-slate-700/50 cursor-pointer transition">
                                        <div class="shrink-0 h-10 w-10 rounded-full overflow-hidden bg-slate-700 flex items-center justify-center border border-slate-600 relative shadow-sm">
                                            @if($member->avatar)
                                                <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="text-slate-300 font-bold text-sm">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                            @endif
                                            <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-800"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-200 truncate">{{ $member->name }}</p>
                                            @if($member->bio)
                                                <p class="text-[11px] text-slate-400 truncate" title="{{ $member->bio }}">{{ $member->bio }}</p>
                                            @else
                                                <p class="text-[11px] text-emerald-400 truncate">Active</p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center py-6 text-slate-500">
                                        <p class="text-sm italic">No active members.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- THE WAITING ROOM -->
                <div class="flex-1 flex flex-col items-center justify-center p-10 text-center bg-slate-900/50">
                    <div class="text-6xl mb-4">🔒</div>
                    <h2 class="text-2xl font-bold text-white mb-2">Private Topic</h2>
                    <p class="text-slate-400 mb-6 max-w-md">This topic is private. You need approval from the author to view or send messages.</p>
                    
                    @if(!$participant)
                        <button wire:click="requestAccess({{ $activeTopic }})" class="bg-green-600 hover:bg-green-500 px-6 py-3 rounded-lg text-white font-medium transition shadow-lg">
                            Request Access
                        </button>
                    @else
                        <div class="bg-slate-800 border border-slate-700 px-6 py-3 rounded-lg text-slate-300 flex items-center gap-2 shadow-sm">
                            <svg class="animate-spin h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Access Request Pending...
                        </div>
                    @endif
                </div>
            @endif

        @else
            <!-- ZERO-DATA STATE -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 bg-slate-900/50">
                <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mb-6 shadow-xl border border-slate-700">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </div>
                
                <h2 class="text-3xl font-bold text-white mb-2">Welcome to Discussions</h2>
                <p class="text-slate-400 max-w-md text-center mb-10">Select a topic from the sidebar to start chatting, or explore new groups to expand your network.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl">
                    <x-modal-trigger>
                        <button class="flex flex-col items-center p-6 w-full bg-slate-800 border border-slate-700 rounded-2xl hover:border-green-500 hover:bg-slate-800/80 transition group text-center cursor-pointer">
                            <div class="w-12 h-12 bg-green-500/10 text-green-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></div>
                            <h3 class="text-white font-bold mb-2">Create a Group</h3>
                            <p class="text-xs text-slate-400">Start a new study circle, define your topics, and manage members.</p>
                        </button>
                    </x-modal-trigger>

                    <button type="button" wire:click="$dispatch('open-join-modal')" class="flex flex-col items-center p-6 bg-slate-800 border border-slate-700 rounded-2xl hover:border-emerald-500 hover:bg-slate-800/80 transition group text-center cursor-pointer">
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div>
                        <h3 class="text-white font-bold mb-2">Join a Group</h3>
                        <p class="text-xs text-slate-400">Browse existing active groups and join the ongoing academic discussions.</p>
                    </button>

                    <button class="flex flex-col items-center p-6 bg-slate-800 border border-slate-700 rounded-2xl hover:border-blue-500 hover:bg-slate-800/80 transition group text-center cursor-pointer relative overflow-hidden">
                        <span class="absolute top-3 right-3 text-[10px] uppercase tracking-wider font-bold bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded-full">Soon</span>
                        <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
                        <h3 class="text-white font-bold mb-2">Invite Discussants</h3>
                        <p class="text-xs text-slate-400">Send an email invitation link to peers to directly join your specific topic.</p>
                    </button>
                </div>
            </div>
        @endif
    </div>
    
    <!-- TOPIC CREATION MODAL -->
    @if($showTopicModal)
    <div class="fixed inset-0 bg-slate-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-slate-800 p-6 rounded-xl w-96 shadow-2xl border border-slate-700">
            <h3 class="text-lg font-semibold text-white mb-4">Create New Topic</h3>
            
            <div class="mb-4">
                <label class="block text-slate-400 text-sm mb-2">Topic Title</label>
                <input type="text" wire:model="newTopicTitle" class="w-full p-3 bg-slate-900 border border-slate-700 text-white rounded-lg focus:outline-none focus:border-green-500" placeholder="e.g., Assignment 1 Discussion">
                @error('newTopicTitle') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-slate-400 text-sm mb-2">Description</label>
                <textarea wire:model="newTopicDescription" rows="3" class="w-full p-3 bg-slate-900 border border-slate-700 text-white rounded-lg focus:outline-none focus:border-green-500 resize-none" placeholder="Briefly describe what this topic is about..."></textarea>
                @error('newTopicDescription') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6 flex items-center">
                <input type="checkbox" wire:model="isPrivate" id="isPrivateCheckbox" class="w-4 h-4 text-green-600 bg-slate-900 border-slate-700 rounded focus:ring-green-500 focus:ring-2 cursor-pointer">
                <label for="isPrivateCheckbox" class="ml-2 text-sm text-slate-300 cursor-pointer">
                    Make this topic private (Requires approval to join)
                </label>
            </div>

            <div class="flex justify-end gap-3 mt-2">
                <button wire:click="$set('showTopicModal', false)" class="px-4 py-2 text-slate-400 hover:text-white transition">Cancel</button>
                <button wire:click="saveTopic" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition">Save Topic</button>
            </div>
        </div>
    </div>
    @endif

    <livewire:student.create-group-modal />
    <livewire:student.join-group-modal />
</div>