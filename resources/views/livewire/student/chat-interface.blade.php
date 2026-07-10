<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On; // Don't forget this import!
use App\Services\PostService;
use App\Models\Group;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $expandedGroup = null; 
    public $activeTopic = null; 
    public $activeGroup = null; // Added this property!

    #[Validate('required|string|max:2000')]
    public $newMessage = '';
    
    public bool $showLeftSidebar = true;
    public bool $showRightSidebar = true;
    public bool $showCreateModal = false;
    public bool $showJoinModal = false;

    public function sendMessage(PostService $postService)
    {
        if (!$this->activeTopic) return;
        $this->validate(); 

        // 1. Save to Database
        $postService->createPost(
            ['content' => $this->newMessage], 
            $this->activeTopic
        );

        // 2. Clear the input box
        $this->newMessage = '';
        
        // 3. Dispatch the browser event to trigger the Alpine.js auto-scroll
        $this->dispatch('message-sent'); 
    }

    // 2. Listen for messages from OTHERS using Livewire's built-in Echo integration
    public function getListeners()
    {
        if (!$this->activeTopic) {
            return [];
        }

        return [
            // Listen to the specific topic channel
            "echo-private:topic.{$this->activeTopic},MessageSent" => 'render'
        ];
    }
        public function refreshMessages()
    {

    }

    public function with(PostService $postService): array
    {
        $formattedMessages = collect();
        
        if ($this->activeTopic) {
            // We pass the ID directly, no need to do Topic::find() first!
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
        }

        return [
            'messages'        => $formattedMessages,
            'groups'          => Auth::user()->groups()->with('topics')->latest()->get(),
            'availableGroups' => Group::whereDoesntHave('members', function($query) {
                $query->where('user_id', Auth::id());
            })->take(10)->get(),
            'activeMembers'   => $this->activeGroup 
                ? $this->activeGroup->members()->get() 
                : collect()
        ];
    }

    public function toggleGroup($groupId)
    {
        $this->expandedGroup = $this->expandedGroup === $groupId ? null : $groupId;
    }

    public function joinGroup($groupId)
    {
        // Attach the current user to the group via the pivot table
        Auth::user()->groups()->attach($groupId);
        
        // Close the modal
        $this->showJoinModal = false;
        
        $this->dispatch('group-joined'); 
    }

    public function selectTopic($topicId)
    {
        $this->activeTopic = $topicId;
        // Find the group so the sidebar/members list knows who is active
        $topic = Topic::find($topicId);
        $this->activeGroup = $topic ? $topic->group : null;
        $this->showRightSidebar = true; 
    }

    #[On('group-created')]
    public function updateSidebar()
    {
        // Leaving this empty is fine! 
        // Just being present forces a refresh of the 'with' data.
    }

    // ... your other methods (sendMessage, toggleGroup) ...
}; ?>

<div class="h-full w-full bg-slate-900 flex overflow-hidden">
    
    <!-- LEFT PANEL: Groups & Topics -->
    <div class="bg-slate-800 border-r border-slate-700 flex flex-col flex-shrink-0 transition-all duration-300 {{ $showLeftSidebar ? 'w-80' : 'w-16' }}">
        
        <!-- Left Panel Header -->
        <div class="h-16 p-4 border-b border-slate-700 bg-slate-800 flex items-center {{ $showLeftSidebar ? 'justify-between' : 'justify-center' }}">
            @if($showLeftSidebar)
                <h2 class="text-lg font-bold text-white truncate">Your Groups</h2>
                <div class="flex items-center gap-1">
                    <!-- Quick Add Button -->
                <x-modal-trigger>
                    <button class="p-1.5 bg-purple-600/20 text-purple-400 hover:bg-purple-600 hover:text-white rounded-md transition" title="Create or Join Group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </x-modal-trigger>
                    <!-- Toggle Sidebar Button -->
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
        
        <!-- Left Panel Body -->
        <div class="flex-1 overflow-y-auto">
            @if($showLeftSidebar)
                <!-- EXPANDED VIEW: Search & Accordions -->
                <div class="p-4 pt-2">
                    <input type="text" placeholder="Search topics..." class="w-full bg-slate-900 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 mb-2 shadow-inner">
                </div>
                
                <div class="px-2 space-y-1 pb-4">
            @forelse($groups as $group)
                <div>
                    <button wire:click="toggleGroup({{ $group->id }})" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-slate-700 transition">
                        <!-- Access object property instead of array key -->
                        <span class="font-semibold text-slate-200">{{ $group->name }}</span>
                        
                        <svg class="w-4 h-4 text-slate-400 transition-transform {{ $expandedGroup === $group->id ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    @if($expandedGroup === $group->id)
                        <div class="mt-1 mb-2 ml-4 space-y-1 border-l-2 border-slate-700 pl-2">
                            @foreach($group->topics as $topic) <!-- $group->topics is now the collection -->
                                <button wire:click="selectTopic({{ $topic->id }})" class="w-full flex items-center justify-between p-2 rounded-lg text-sm transition {{ $activeTopic == $topic->id ? 'bg-purple-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                                    <span class="truncate pr-2"># {{ $topic->title }}</span>
                                    <!-- Replace 'unread' with a real column if you have one, or remove if not yet implemented -->
                                </button>
                            @endforeach
                            
                            <!-- Add Topic Button -->
                            <button class="w-full flex items-center gap-2 p-2 rounded-lg text-sm text-slate-500 hover:text-purple-400 hover:bg-slate-700 transition mt-1">
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
                <!-- COLLAPSED VIEW: Group Icons Only -->
                <div class="flex flex-col items-center py-4 space-y-4">
                    @foreach($groups as $group)
                        <button wire:click="$set('showLeftSidebar', true); toggleGroup({{ $group['id'] }})" class="w-10 h-10 rounded-xl bg-slate-700 hover:bg-purple-600 flex items-center justify-center text-sm font-bold text-white transition shadow-md group relative" title="{{ $group['name'] }}">
                            {{ $group['initials'] }}
                            @if(collect($group['topics'])->sum('unread') > 0)
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 border-2 border-slate-800 rounded-full"></span>
                            @endif
                        </button>
                    @endforeach
                    <!-- Collapsed Add Group Button -->
                    <button wire:click="$set('showLeftSidebar', true)" class="w-10 h-10 rounded-xl border border-dashed border-slate-600 text-slate-400 hover:border-purple-500 hover:text-purple-400 flex items-center justify-center transition" title="Add Group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- CENTER PANEL: Dynamic Content Area -->
    <div class="flex-1 flex flex-col bg-slate-900 relative min-w-0">
        
        @if($activeTopic)
            <!-- ========================================== -->
            <!-- ACTIVE CHAT INTERFACE (Shows when topic is selected) -->
            <!-- ========================================== -->
            
            <div class="h-16 border-b border-slate-700 bg-slate-800 flex items-center px-6 flex-shrink-0 justify-between">
                <div>
                    <h3 class="text-white font-bold text-lg leading-tight flex items-center gap-2">
                        <span class="text-slate-500">#</span> Topic Name
                    </h3>
                    <p class="text-xs text-slate-400">Group Name</p>
                </div>
            </div>

            <!-- Messages Area -->
            <div 
                wire:poll.2s.visible="refreshMessages"
                x-data="{ scroll() { $el.scrollTop = $el.scrollHeight; } }"
                x-init="scroll()"
                x-on:message-sent.window="setTimeout(scroll, 100)" 
                class="flex-1 overflow-y-auto p-6 space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-700 hover:[&::-webkit-scrollbar-thumb]:bg-slate-600 [&::-webkit-scrollbar-thumb]:rounded-full"
            >
                
                @forelse($messages as $message)
                    <div wire:key="msg-{{ $message['id'] }}" class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] lg:max-w-[70%]">
                            
                            <!-- SENDER'S NAME -->
                            @if(!$message['is_mine'])
                                <span class="text-xs text-slate-400 ml-1 mb-1 block">
                                    {{ $message['sender'] }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400 mr-1 mb-1 block text-right">
                                    You
                                </span>
                            @endif

                            <!-- THE MESSAGE BUBBLE -->
                            <div class="p-3 rounded-2xl shadow-sm {{ $message['is_mine'] ? 'bg-purple-600 text-white rounded-tr-none' : 'bg-slate-800 text-slate-200 border border-slate-700 rounded-tl-none' }}">
                                {{ $message['text'] }}
                            </div>
                            
                            <!-- THE TIME -->
                            <span class="text-[10px] text-slate-500 mt-1 block {{ $message['is_mine'] ? 'text-right mr-1' : 'ml-1' }}">
                                {{ $message['time'] }}
                            </span>

                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-slate-500">
                        <svg class="w-12 h-12 mb-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                @endforelse

            </div>
                <!-- Message Input -->
                <div class="p-4 bg-slate-800 border-t border-slate-700">
                    <form wire:submit="sendMessage" class="flex items-end gap-2 relative">
                        <button type="button" class="p-2 text-slate-400 hover:text-purple-400 transition rounded-full hover:bg-slate-700 mb-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </button>
                        <textarea wire:model="newMessage" rows="1" placeholder="Type a message..." class="flex-1 bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 resize-none shadow-inner"></textarea>
                        <button type="submit" class="p-3 bg-purple-600 hover:bg-purple-500 text-white rounded-xl transition shadow-lg flex-shrink-0 mb-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                        </button>
                        @error('newMessage') <span class="absolute -top-6 left-12 text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </form>
            </div>

        @else
            <!-- ========================================== -->
            <!-- ZERO-DATA STATE (Shows when NO topic is selected) -->
            <!-- ========================================== -->
            
            <div class="flex-1 flex flex-col items-center justify-center p-8 bg-slate-900/50">
                
                <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mb-6 shadow-xl border border-slate-700">
                    <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </div>
                
                <h2 class="text-3xl font-bold text-white mb-2">Welcome to Discussions</h2>
                <p class="text-slate-400 max-w-md text-center mb-10">Select a topic from the sidebar to start chatting, or explore new groups to expand your network.</p>

                <!-- Action Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl">
                    
                    <!-- Create Group Action -->
                    <x-modal-trigger>
                    <button class="flex flex-col items-center p-6 bg-slate-800 border border-slate-700 rounded-2xl hover:border-purple-500 hover:bg-slate-800/80 transition group text-center cursor-pointer">
                        <div class="w-12 h-12 bg-purple-500/10 text-purple-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h3 class="text-white font-bold mb-2">Create a Group</h3>
                        <p class="text-xs text-slate-400">Start a new study circle, define your topics, and manage members.</p>
                    </button>
                    </x-modal-trigger>

                    <!-- Join Group Action -->
                    <button type="button" wire:click="$dispatch('open-join-modal')" class="flex flex-col items-center p-6 bg-slate-800 border border-slate-700 rounded-2xl hover:border-emerald-500 hover:bg-slate-800/80 transition group text-center cursor-pointer">
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-white font-bold mb-2">Join a Group</h3>
                        <p class="text-xs text-slate-400">Browse existing active groups and join the ongoing academic discussions.</p>
                    </button>

                    <!-- Invite Action (Future Proofing) -->
                    <button class="flex flex-col items-center p-6 bg-slate-800 border border-slate-700 rounded-2xl hover:border-blue-500 hover:bg-slate-800/80 transition group text-center cursor-pointer relative overflow-hidden">
                        <!-- Coming Soon Badge -->
                        <span class="absolute top-3 right-3 text-[10px] uppercase tracking-wider font-bold bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded-full">Soon</span>
                        
                        <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-white font-bold mb-2">Invite Discussants</h3>
                        <p class="text-xs text-slate-400">Send an email invitation link to peers to directly join your specific topic.</p>
                    </button>
                    
                </div>
            </div>
        @endif
    </div>

    <!-- RIGHT PANEL: Active Members (Only show if a topic is active) -->
    @if($activeTopic)
        <div class="bg-slate-800 border-l border-slate-700 flex flex-col flex-shrink-0 transition-all duration-300 {{ $showRightSidebar ? 'w-64' : 'w-16' }}">
            
            <div class="h-16 p-4 border-b border-slate-700 bg-slate-800 flex items-center {{ $showRightSidebar ? 'justify-between' : 'justify-center' }}">
                <button wire:click="$toggle('showRightSidebar')" class="text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-700 transition" title="Toggle Members">
                    @if($showRightSidebar)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    @endif
                </button>
                
                @if($showRightSidebar)
                    <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider truncate">Group Members</h2>
                @endif
            </div>
            
            <!-- Right Panel Body -->
<div class="p-4 space-y-4">
    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Group Members</h3>
    
    @forelse($activeMembers as $member)
        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-800/50 cursor-pointer transition">
            
            <!-- AVATAR -->
            <div class="shrink-0 h-10 w-10 rounded-full overflow-hidden bg-slate-700 flex items-center justify-center border border-slate-600 relative">
                @if($member->avatar)
                    <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                @else
                    <!-- Fallback if no avatar is uploaded -->
                    <span class="text-slate-300 font-bold text-sm">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </span>
                @endif
                
                <!-- Optional: A tiny green dot to show they are "Active" -->
                <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-900"></span>
            </div>

            <!-- NAME AND BIO -->
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-200 truncate">
                    {{ $member->name }}
                </p>
                
                <!-- Display the Bio if they have one, otherwise just say Active -->
                @if($member->bio)
                    <p class="text-[10px] text-slate-400 truncate" title="{{ $member->bio }}">
                        {{ $member->bio }}
                    </p>
                @else
                    <p class="text-[10px] text-emerald-400 truncate">
                        Active
                    </p>
                @endif
            </div>

        </div>
    @empty
        <p class="text-xs text-slate-500 italic text-center mt-10">No members found.</p>
    @endforelse
    </div>
        </div>
    @endif
    <livewire:student.create-group-modal />
    <livewire:student.join-group-modal />
</div>