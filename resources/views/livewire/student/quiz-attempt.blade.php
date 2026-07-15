<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    public Quiz $quiz;
    public $questions;
    public int $currentQuestionIndex = 0;
    public array $answers = []; 
    public int $timeRemaining = 0;
    public QuizAttempt $attempt;

    public function mount($id) 
    {
        // 1. Load the Quiz and Questions
        $this->quiz = Quiz::with('questions')->findOrFail($id);
        $this->questions = $this->quiz->questions;

        // 2. Start or Resume the Attempt
        $this->attempt = QuizAttempt::firstOrCreate(
            ['quiz_id' => $this->quiz->id, 'user_id' => Auth::id(), 'submitted_at' => null],
            ['start_time' => now(), 'violations' => 0]
        );

        // 3. Calculate accurate time remaining (prevents cheating by refreshing the page)
        $elapsedSeconds = now()->diffInSeconds($this->attempt->start_time);
        $totalSeconds = $this->quiz->time_limit * 60;
        $this->timeRemaining = max(0, $totalSeconds - $elapsedSeconds);

        // Pre-fill answers array
        foreach ($this->questions as $q) {
            $this->answers[$q->id] = null;
        }
    }

    public function setQuestion($index)
    {
        $this->currentQuestionIndex = $index;
    }

    public function logViolation() 
    {
        // Increment the violation counter in the database instantly
        $this->attempt->increment('violations');
    }

    public function submitAttempt(QuizService $quizService, $autoSubmitted = false)
    {
        // Pass the data to the Brain!
        $score = $quizService->submitAttempt($this->attempt->id, $this->answers, $autoSubmitted);
        
        session()->flash('success', "Quiz submitted! Your score: $score");
        return redirect()->route('dashboard'); // Or wherever you want them to go
    }
}; ?>

<!-- Full-screen layout to enforce the lockdown requirement[cite: 4] -->
<div x-data="{
    trackViolation() {
        // Instead of just an alert, call a Livewire method to log the event
        @this.call('logViolation');
        alert('Warning: Leaving the quiz window is recorded.');
    }
}" @blur.window="trackViolation()">
<!-- Inject the real time from Livewire -->
<div class="min-h-screen ..." x-data="quizAttempt({{ $timeRemaining }})"><!-- 875 seconds = 14:35 -->
    
    <!-- TOP NAVIGATION BAR[cite: 5] -->
    <header class="h-16 bg-green-700 flex items-center justify-between px-6 shadow-md z-20 shrink-0">
        <!-- Exit Quiz Button[cite: 5] -->
        <button type="button" class="flex items-center gap-2 text-white/90 hover:text-white transition group">
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-sm">Exit Quiz</span>
        </button>

        <!-- Quiz Title[cite: 5] -->
        <h1 class="text-lg font-bold tracking-wide hidden md:block">Data Structures Quiz</h1>

        <!-- Timer[cite: 4, 5] -->
        <div class="flex items-center gap-2 bg-green-900/40 px-4 py-1.5 rounded-full border border-green-600/50 shadow-inner">
            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium text-green-100 hidden sm:block">Time Remaining</span>
            <span class="font-mono font-bold text-white tracking-wider" x-text="formatTime(timeRemaining)">00:14:35</span>
        </div>
    </header>

    <!-- MAIN WORKSPACE -->
    <div class="flex-1 flex overflow-hidden relative">
        
        <!-- LEFT SIDEBAR: Question Navigation[cite: 5] -->
        <aside class="w-72 bg-slate-800 border-r border-slate-700 flex flex-col shrink-0 z-10 shadow-xl">
            <div class="p-5 flex-1 overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-600">
                <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-5">Questions</h2>
                
                <!-- Number Grid[cite: 5] -->
@php
    $currentQuestion = $questions[$currentQuestionIndex];
@endphp

<div class="mb-10">
    <h3 class="text-green-400 font-bold text-sm tracking-widest uppercase mb-4">
        Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
    </h3>
    <p class="text-2xl text-white font-medium leading-relaxed">
        {{ $currentQuestion->text }}
    </p>
</div>

<!-- Dynamic Options -->
<div class="space-y-4 flex-1">
    @foreach(json_decode($currentQuestion->options) as $key => $optionText)
        <label class="flex items-center gap-4 p-5 rounded-xl border transition cursor-pointer
            {{ $answers[$currentQuestion->id] === $key ? 'border-green-600 bg-green-900/10' : 'border-slate-700 bg-slate-800/50 hover:border-green-500/50' }}">
            
            <!-- Wire model directly binds to the Livewire answers array -->
            <input type="radio" 
                   wire:model.live="answers.{{ $currentQuestion->id }}" 
                   value="{{ $key }}" 
                   class="w-5 h-5 text-green-600 bg-slate-900 border-slate-500">
            
            <span class="text-slate-300 text-lg">{{ $key }}. {{ $optionText }}</span>
        </label>
    @endforeach
</div>

                <!-- Legend[cite: 5] -->
                <div class="space-y-3 bg-slate-900/30 p-4 rounded-xl border border-slate-700/50">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded shadow-sm bg-green-600"></span>
                        <span class="text-xs text-slate-300 font-semibold uppercase tracking-wide">Answered</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded shadow-sm bg-orange-500"></span>
                        <span class="text-xs text-slate-300 font-semibold uppercase tracking-wide">Review</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded shadow-sm bg-slate-600"></span>
                        <span class="text-xs text-slate-300 font-semibold uppercase tracking-wide">Not Answered</span>
                    </div>
                </div>
            </div>

            <!-- Mark for Review Action[cite: 5] -->
            <div class="p-5 border-t border-slate-700 bg-slate-800">
                <button class="w-full py-2.5 rounded-lg border-2 border-slate-600 text-slate-300 hover:bg-slate-700 hover:text-white transition font-medium flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    Mark for Review
                </button>
            </div>
        </aside>

        <!-- CENTER: Question Area[cite: 5] -->
        <main class="flex-1 flex flex-col bg-slate-900 overflow-y-auto">
            <div class="max-w-4xl mx-auto w-full flex-1 flex flex-col p-8 lg:p-12">
                
                <!-- Question Header[cite: 5] -->
                <div class="mb-10">
                    <h3 class="text-green-400 font-bold text-sm tracking-widest uppercase mb-4">Question 1 of 16</h3>
                    <p class="text-2xl text-white font-medium leading-relaxed">Which of the following is a linear data structure?</p>
                </div>

                <!-- Multiple Choice Options[cite: 5] -->
                <div class="space-y-4 flex-1">
                    
                    <label class="flex items-center gap-4 p-5 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-800 hover:border-green-500/50 cursor-pointer transition group">
                        <input type="radio" name="q1" value="A" class="w-5 h-5 text-green-600 bg-slate-900 border-slate-500 focus:ring-green-600 focus:ring-offset-slate-800">
                        <span class="text-slate-300 group-hover:text-white text-lg">A. Tree</span>
                    </label>

                    <label class="flex items-center gap-4 p-5 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-800 hover:border-green-500/50 cursor-pointer transition group">
                        <input type="radio" name="q1" value="B" class="w-5 h-5 text-green-600 bg-slate-900 border-slate-500 focus:ring-green-600 focus:ring-offset-slate-800">
                        <span class="text-slate-300 group-hover:text-white text-lg">B. Graph</span>
                    </label>

                    <!-- Selected State Example[cite: 5] -->
                    <label class="flex items-center gap-4 p-5 rounded-xl border-2 border-green-600 bg-green-900/10 cursor-pointer transition">
                        <input type="radio" name="q1" value="C" checked class="w-5 h-5 text-green-600 bg-slate-900 border-green-600 focus:ring-green-600 focus:ring-offset-slate-800">
                        <span class="text-white font-medium text-lg">C. Stack</span>
                    </label>

                    <label class="flex items-center gap-4 p-5 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-800 hover:border-green-500/50 cursor-pointer transition group">
                        <input type="radio" name="q1" value="D" class="w-5 h-5 text-green-600 bg-slate-900 border-slate-500 focus:ring-green-600 focus:ring-offset-slate-800">
                        <span class="text-slate-300 group-hover:text-white text-lg">D. Heap</span>
                    </label>

                </div>

                <!-- Bottom Navigation[cite: 5] -->
                <div class="mt-12 pt-6 border-t border-slate-800 flex items-center justify-between">
                    <button class="px-6 py-3 rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Previous
                    </button>
                    
                    <button class="px-8 py-3 rounded-lg bg-green-600 text-white font-bold hover:bg-green-500 transition shadow-lg shadow-green-900/20 flex items-center gap-2">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                
                <!-- Submit Action[cite: 5] -->
                <div class="mt-6 flex justify-end">
                    <button @click="submitQuiz()" class="w-full md:w-auto px-10 py-3.5 rounded-lg bg-green-700 text-white font-extrabold tracking-wide hover:bg-green-600 transition shadow-xl flex items-center justify-center gap-2 border border-green-500">
                        <svg class="w-5 h-5 transform -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Submit Quiz
                    </button>
                </div>

            </div>
        </main>
    </div>
</div>
</div>



<!-- Alpine.js script to handle the strict countdown requirement[cite: 4] -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quizAttempt', (initialSeconds) => ({
            timeRemaining: initialSeconds,
            timerInterval: null,

            init() {
                this.timerInterval = setInterval(() => {
                    if (this.timeRemaining > 0) {
                        this.timeRemaining--;
                    } else {
                        clearInterval(this.timerInterval);
                        this.forceSubmit(); // Auto-submits itself[cite: 4]
                    }
                }, 1000);
            },

            formatTime(seconds) {
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                return [
                    h > 0 ? String(h).padStart(2, '0') : null,
                    String(m).padStart(2, '0'),
                    String(s).padStart(2, '0')
                ].filter(Boolean).join(':');
            },

            submitQuiz() {
                if(confirm('Are you sure you want to submit your quiz?')) {
                    clearInterval(this.timerInterval);
                    // Wire to Livewire backend method[cite: 5]
                    @this.call('submitAttempt');
                }
            },

            forceSubmit() {
                alert('Time has expired. Your quiz will now be submitted automatically.');
                @this.call('submitAttempt', true); // Passes 'true' for $autoSubmitted
            }       
        }));
    });
</script>