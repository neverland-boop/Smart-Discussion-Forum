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
    public array $markedForReview = [];
    public int $timeRemaining = 0;
    public QuizAttempt $attempt;

    public function mount($id) 
    {
        // 1. Load the Quiz and Questions
        $this->quiz = Quiz::with('questions')->findOrFail($id);
        $this->questions = $this->quiz->questions;
        if ($this->quiz->start_time && now()->isBefore($this->quiz->start_time)) {
                    session()->flash('error', 'This quiz has not started yet. Please wait until ' . \Carbon\Carbon::parse($this->quiz->start_time)->format('M d, Y h:i A'));
                    return redirect()->route('dashboard'); // Redirect them back to the quiz list
                }
        // 2. Start or Resume the Attempt
        $this->attempt = QuizAttempt::firstOrCreate(
            ['quiz_id' => $this->quiz->id, 'user_id' => Auth::id(), 'submitted_at' => null],
            ['start_time' => now(), 'violations' => 0]
        );

        // 3. Calculate accurate time remaining
        $elapsedSeconds = now()->diffInSeconds($this->attempt->start_time);
        $totalSeconds = $this->quiz->time_limit * 60;
        $this->timeRemaining = max(0, $totalSeconds - $elapsedSeconds);

        // Pre-fill tracking arrays
        foreach ($this->questions as $q) {
            $this->answers[$q->id] = null;
            $this->markedForReview[$q->id] = false;
        }
    }

    public function setQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function toggleReview()
    {
        $currentId = $this->questions[$this->currentQuestionIndex]->id;
        $this->markedForReview[$currentId] = !$this->markedForReview[$currentId];
    }

    public function logViolation() 
    {
        // Increment the violation counter in the database
        $this->attempt->increment('violations');
    }

    public function submitAttempt(QuizService $quizService, $autoSubmitted = false)
    {
        // Pass the answers to the QuizService
        $score = $quizService->submitAttempt($this->attempt->id, $this->answers, $autoSubmitted);
        
        session()->flash('success', "Quiz submitted! Your score is pending review or calculated as: $score");
        return redirect()->route('dashboard'); 
    }
}; ?>

<!-- Full-screen layout to enforce the lockdown requirement -->
<div x-data="{
    trackViolation() {
        // Log the violation in the database immediately
        @this.call('logViolation');
        
        // Open the global modal instead of native alert
        $dispatch('open-confirm', { 
            title: 'Academic Violation Detected', 
            message: 'Leaving the quiz window is recorded. If you continue to navigate away, your quiz will be auto-submitted.', 
            callback: () => { 
                // Auto-submit if they insist on leaving
                @this.call('submitAttempt', true); 
            } 
        });
    }
}" @blur.window="trackViolation()">

    <div class="min-h-screen h-screen w-full bg-slate-900 flex flex-col font-sans text-slate-50 overflow-hidden" 
         x-data="quizAttempt({{ $timeRemaining }})">
        
        <!-- TOP NAVIGATION BAR -->
        <header class="h-16 bg-green-700 flex items-center justify-between px-6 shadow-md z-20 shrink-0">
            <!-- Exit Quiz Button -->
            <button @click="submitQuiz()" type="button" class="flex items-center gap-2 text-white/90 hover:text-white transition group">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="font-semibold text-sm">Exit & Submit</span>
            </button>

            <!-- Quiz Title -->
            <h1 class="text-lg font-bold tracking-wide hidden md:block">{{ $quiz->title }}</h1>

            <!-- Timer -->
            <div class="flex items-center gap-2 bg-green-900/40 px-4 py-1.5 rounded-full border border-green-600/50 shadow-inner">
                <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-green-100 hidden sm:block">Time Remaining</span>
                <span class="font-mono font-bold text-white tracking-wider" x-text="formatTime(timeRemaining)">--:--:--</span>
            </div>
        </header>

        <!-- MAIN WORKSPACE -->
        <div class="flex-1 flex overflow-hidden relative">
            
            <!-- LEFT SIDEBAR: Question Navigation -->
            <aside class="w-72 bg-slate-800 border-r border-slate-700 flex flex-col shrink-0 z-10 shadow-xl">
                <div class="p-5 flex-1 overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-600">
                    <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider mb-5">Questions</h2>
                    
                    <!-- Number Grid -->
                    <div class="grid grid-cols-4 gap-2.5 mb-8">
                        @foreach($questions as $index => $question)
                            <button 
                                wire:click="setQuestion({{ $index }})"
                                class="h-10 rounded-md transition font-bold shadow-sm 
                                {{ $currentQuestionIndex === $index ? 'ring-2 ring-white ring-offset-2 ring-offset-slate-800' : '' }}
                                {{ $markedForReview[$question->id] ? 'bg-orange-500 text-white' : ($answers[$question->id] !== null ? 'bg-green-600 text-white' : 'bg-slate-700 text-slate-300 hover:bg-slate-600') }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Legend -->
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

                <!-- Mark for Review Action -->
                <div class="p-5 border-t border-slate-700 bg-slate-800">
                    <button wire:click="toggleReview" class="w-full py-2.5 rounded-lg border-2 border-slate-600 text-slate-300 hover:bg-slate-700 hover:text-white transition font-medium flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        Toggle Review
                    </button>
                </div>
            </aside>

            <!-- CENTER: Question Area -->
            <!-- CENTER: Question Area -->
<main class="flex-1 flex flex-col bg-slate-900 overflow-y-auto">
    @php
        // Safely grab the question, re-indexing keys to ensure 0, 1, 2... format
        $currentQuestion = collect($questions)->values()->get($currentQuestionIndex);
    @endphp

    @if($currentQuestion)
        <div class="max-w-4xl mx-auto w-full flex-1 flex flex-col p-8 lg:p-12">
            
            <!-- Question Header -->
            <div class="mb-10">
                <h3 class="text-green-400 font-bold text-sm tracking-widest uppercase mb-4">
                    Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                </h3>
                <p class="text-2xl text-white font-medium leading-relaxed">
                    {{ $currentQuestion->text ?? '' }}
                </p>
            </div>

            <!-- Multiple Choice Options -->
            <div class="space-y-4 flex-1">
                @php
                    $options = is_string($currentQuestion->options) 
                        ? json_decode($currentQuestion->options, true) 
                        : $currentQuestion->options;
                @endphp

                @if($options)
                    @foreach($options as $key => $optionText)
                        <label class="flex items-center gap-4 p-5 rounded-xl border transition cursor-pointer group
                            {{ ($answers[$currentQuestion->id] ?? null) === $key ? 'border-green-600 bg-green-900/10' : 'border-slate-700 bg-slate-800/50 hover:bg-slate-800 hover:border-green-500/50' }}">
                            
                            <input type="radio" 
                                   wire:model.live="answers.{{ $currentQuestion->id }}" 
                                   value="{{ $key }}" 
                                   class="w-5 h-5 text-green-600 bg-slate-900 border-slate-500 focus:ring-green-600 focus:ring-offset-slate-800">
                            
                            <span class="text-lg {{ ($answers[$currentQuestion->id] ?? null) === $key ? 'text-white font-medium' : 'text-slate-300 group-hover:text-white' }}">
                                {{ $key }}. {{ $optionText }}
                            </span>
                        </label>
                    @endforeach
                @endif
            </div>

            <!-- Bottom Navigation -->
            <div class="mt-12 pt-6 border-t border-slate-800 flex items-center justify-between">
                <button 
                    wire:click="setQuestion({{ $currentQuestionIndex - 1 }})"
                    @if($currentQuestionIndex === 0) disabled @endif
                    class="px-6 py-3 rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition font-medium flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Previous
                </button>
                
                @if($currentQuestionIndex < count($questions) - 1)
                    <button 
                        wire:click="setQuestion({{ $currentQuestionIndex + 1 }})"
                        class="px-8 py-3 rounded-lg bg-green-600 text-white font-bold hover:bg-green-500 transition shadow-lg shadow-green-900/20 flex items-center gap-2">
                        Next
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @endif
            </div>
            
            <!-- Submit Action -->
            <div class="mt-6 flex justify-end">
                <!-- Ensure @click points to submitQuiz() defined in your Alpine data -->
                <button @click="submitQuiz()" 
                        type="button" 
                        class="w-full md:w-auto px-10 py-3.5 rounded-lg bg-green-700 text-white font-extrabold tracking-wide hover:bg-green-600 transition shadow-xl flex items-center justify-center gap-2 border border-green-500">
                    <svg class="w-5 h-5 transform -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Submit Quiz
                </button>
            </div>

        </div>
    @else
        <!-- Friendly Empty State if Quiz has no questions -->
        <div class="flex-1 flex flex-col items-center justify-center p-8">
            <svg class="w-16 h-16 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h2 class="text-2xl font-bold text-white mb-2">No Questions Found</h2>
            <p class="text-slate-400 text-center max-w-md">Your instructor hasn't added any questions to this quiz yet.</p>
            <button @click="submitQuiz()" class="mt-6 px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">Exit Quiz</button>
        </div>
    @endif
</main>

        </div>
    </div>
</div>

<!-- Alpine.js script to handle the strict countdown requirement -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quizAttempt', (initialSeconds) => ({
            timeRemaining: initialSeconds,
            timerInterval: null,

            init() {
                if (this.timeRemaining > 0) {
                    this.timerInterval = setInterval(() => {
                        if (this.timeRemaining > 0) {
                            this.timeRemaining--;
                        } else {
                            clearInterval(this.timerInterval);
                            this.forceSubmit();
                        }
                    }, 1000);
                } else {
                    this.forceSubmit(); // Submit immediately if loaded with 0 time
                }
            },

            formatTime(seconds) {
                if (seconds <= 0) return '00:00';
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                return [
                    h > 0 ? String(h).padStart(2, '0') : null,
                    String(m).padStart(2, '0'),
                    String(s).padStart(2, '0')
                ].filter(Boolean).join(':');
            },

// Inside your quiz-attempt.blade.php script
        submitQuiz() {
            this.$dispatch('open-confirm', {
                title: 'Submit Quiz?',
                message: 'Are you sure? You cannot change your answers after submission.',
                callback: () => {
                    clearInterval(this.timerInterval);
                    @this.call('submitAttempt', false);
                }
            });
        }

            forceSubmit() {
                alert('Time has expired. Your quiz will now be submitted automatically.');
                @this.call('submitAttempt', true);
            }
        }));
    });
</script>