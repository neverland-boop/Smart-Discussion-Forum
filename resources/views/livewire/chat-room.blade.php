<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public string $messageBody = '';
    public array $activeChannels = [
        ['name' => 'General Academic Feed', 'messages' => 142, 'active' => true],
        ['name' => 'CS_4102 Advanced OS Questions', 'messages' => 24, 'active' => false],
        ['name' => 'Sprint 2 Setup Queries', 'messages' => 18, 'active' => false]
    ];
    public array $chatFeed = [
        ['user' => 'Student_Anthony', 'time' => '11:42', 'body' => 'Has anyone figured out the third parameter for the process syncing assignment?'],
        ['user' => 'Student_Patience', 'time' => '11:45', 'body' => 'Yes, check out the lecture notes from last Tuesday. Dr. Duncan covered it in detail on slide 14.']
    ];

    public function postMessage(): void
    {
        $this->validate(['messageBody' => 'required|string|max:1000']);
        
        $this->chatFeed[] = [
            'user' => 'Dr. Duncan Francis',
            'time' => now()->format('H:i'),
            'body' => $this->messageBody
        ];

        $this->messageBody = '';
    }
}; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full min-h-[500px]">
    
    <!-- Left Column: Channel List (4 out of 12 columns) -->
    <div class="lg:col-span-4 bg-zinc-50 border border-zinc-200 rounded-xl p-4 flex flex-col gap-3">
        <h2 class="text-xs font-black text-zinc-400 uppercase tracking-wider px-1">Forum Channels</h2>
        <div class="flex flex-col gap-1.5">
            @foreach($activeChannels as $channel)
                <button class="w-full text-left text-xs font-bold p-3 rounded-xl transition-all cursor-pointer border-none flex items-center justify-between
                    {{ $channel['active'] 
                        ? 'bg-zinc-900 text-white shadow-sm' 
                        : 'bg-white text-zinc-700 hover:bg-zinc-100 border border-zinc-200' }}">
                    <span># {{ $channel['name'] }}</span>
                    <span class="text-[10px] opacity-60 font-medium">{{ $channel['messages'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Right Column: Interactive Chat Stream (8 out of 12 columns) -->
    <div class="lg:col-span-8 border border-zinc-200 rounded-xl flex flex-col h-full bg-white justify-between overflow-hidden">
        <!-- Feed Stream Area -->
        <div class="flex-1 p-5 overflow-y-auto space-y-4 bg-zinc-50/30">
            @foreach($chatFeed as $chat)
                <div class="flex flex-col {{ $chat['user'] === 'Dr. Duncan Francis' ? 'items-end' : 'items-start' }}">
                    <div class="text-[10px] text-zinc-400 font-bold mb-0.5 px-1">{{ $chat['user'] }} • {{ $chat['time'] }}</div>
                    <div class="max-w-[80%] rounded-xl px-4 py-2.5 text-xs font-medium shadow-sm 
                        {{ $chat['user'] === 'Dr. Duncan Francis' 
                            ? 'bg-[#10b981] text-white rounded-tr-none' 
                            : 'bg-white text-zinc-800 border border-zinc-200 rounded-tl-none' }}">
                        <p class="leading-relaxed">{{ $chat['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Box Area Bar -->
        <div class="p-3 border-t border-zinc-200 bg-white">
            <form wire:submit="postMessage" class="flex gap-2">
                <input 
                    wire:model="messageBody" 
                    type="text" 
                    placeholder="Type an announcement or response to this thread..." 
                    class="flex-1 bg-zinc-50 text-xs border border-zinc-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-inner"
                    required
                />
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs px-5 py-2.5 rounded-lg transition-colors cursor-pointer border-none shadow-sm">
                    Send
                </button>
            </form>
        </div>
    </div>

</div>
