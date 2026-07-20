<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\QuizQuestion;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Layout('components.layouts.app')] class extends Component {
    public string $quizTitle = '';
    public string $categoryClass = '';

    public array $questions = [
        [
            'text' => '',
            'options' => ['', '', '', ''],
            'correct' => 0
        ]
    ];

    public function addQuestion(): void
    {
        $this->questions[] = [
            'text' => '',
            'options' => ['', '', '', ''],
            'correct' => 0
        ];
    }

    public function removeQuestion(int $index): void
    {
        if (count($this->questions) > 1) {
            unset($this->questions[$index]);
            $this->questions = array_values($this->questions);
        }
    }

    public function publishQuiz(): void
    {
        $this->validate([
            'quizTitle' => 'required|string',
            'categoryClass' => 'required',
            'questions.*.text' => 'required|string',
            'questions.*.options.*' => 'required|string',
        ]);

        foreach ($this->questions as $q) {
            QuizQuestion::create([
                'quiz_title' => $this->quizTitle,
                'category_class' => $this->categoryClass,
                'question_text' => $q['text'],
                'options' => $q['options'],
                'correct_option_index' => (int)$q['correct']
            ]);
        }

        $this->reset(['quizTitle', 'categoryClass', 'questions']);
        $this->questions = [['text' => '', 'options' => ['', '', '', ''], 'correct' => 0]];

        session()->flash('status', 'Quiz questions saved successfully!');
    }

    public function getSavedQuestionsProperty()
    {
        return QuizQuestion::latest()->get();
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            echo "Question Text,Option A,Option B,Option C,Option D,Correct Option Index\n";
        }, 'Quiz_Template.csv', ['Content-Type' => 'text/csv']);
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">Quiz Management</h1>
        </div>
        <button wire:click="downloadTemplate" type="button" class="bg-zinc-950 text-white font-bold text-xs px-4 py-2 rounded-xl border-none cursor-pointer">
            💾 Download Template
        </button>
    </div>

    @if (session()->has('status'))
        <div class="text-xs font-semibold bg-emerald-50 text-emerald-700 p-3 rounded-lg border border-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="publishQuiz" class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Form Setup Details -->
        <div class="xl:col-span-7 bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col gap-4">
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Quiz Title</label>
                <input wire:model="quizTitle" type="text" class="w-full bg-zinc-50 dark:bg-zinc-950 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2" required />
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Class Allocation</label>
                <select wire:model="categoryClass" class="w-full bg-zinc-50 dark:bg-zinc-950 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2" required>
                    <option value="">Select Class</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Database Systems">Database Systems</option>
                </select>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-[#24a065] text-white font-bold text-xs px-6 py-2.5 rounded-xl border-none cursor-pointer">
                    Save to Database
                </button>
            </div>
        </div>

        <!-- Question Creation Panel -->
        <div class="xl:col-span-5 p-5 bg-[#52c48a] rounded-2xl flex flex-col gap-4 max-h-[500px] overflow-y-auto shadow-md">
            <div class="flex justify-between items-center border-b border-zinc-950/10 pb-2">
                <span class="font-bold text-xs uppercase text-zinc-950">Questions Matrix</span>
                <button wire:click.prevent="addQuestion" type="button" class="bg-zinc-900 text-white text-[10px] px-2.5 py-1 rounded-lg border-none cursor-pointer">+ Add</button>
            </div>

            @foreach($questions as $index => $q)
                <div wire:key="q-key-{{ $index }}" class="bg-white rounded-xl p-4 flex flex-col gap-3 text-zinc-800 shadow-sm">
                    <div class="flex justify-between items-center text-[10px] font-bold text-zinc-400">
                        <span>QUESTION #{{ $index + 1 }}</span>
                        @if(count($questions) > 1)
                            <button wire:click.prevent="removeQuestion({{ $index }})" type="button" class="text-red-500 border-none bg-transparent cursor-pointer">Remove</button>
                        @endif
                    </div>
                    
                    <input wire:model="questions.{{ $index }}.text" type="text" placeholder="Question statement" class="w-full bg-zinc-50 text-xs border rounded-md p-1.5 focus:outline-none" required />

                    <div class="flex flex-col gap-2">
                        @foreach($q['options'] as $optIdx => $option)
                            <div wire:key="q-{{ $index }}-opt-{{ $optIdx }}" class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-zinc-400 w-3">{{ $optIdx === 0 ? 'A' : ($optIdx === 1 ? 'B' : ($optIdx === 2 ? 'C' : 'D')) }}</span>
                                <input wire:model="questions.{{ $index }}.options.{{ $optIdx }}" type="text" placeholder="Option narrative" class="flex-1 bg-zinc-50 text-[11px] border rounded-md p-1 focus:outline-none" required   />
                                <input type="radio" wire:model="questions.{{ $index }}.correct" value="{{ $optIdx }}" name="correct-rad-{{ $index }}" class="cursor-pointer" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </form>

    <!-- Questions History List -->
    <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100 mb-4 pb-2 border-b border-zinc-100 dark:border-zinc-800">
            Live Saved Questions History Log
        </h3>

        @if($this->savedQuestions->isEmpty())
            <div class="text-center py-6 text-xs text-zinc-400">No questions saved in database yet.</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[350px] overflow-y-auto">
                @foreach($this->savedQuestions as $savedQ)
                    <div wire:key="db-item-{{ $savedQ->id }}" class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800/80 flex flex-col gap-2">
                        <div class="text-[10px] text-zinc-400 font-bold uppercase">🏫 {{ $savedQ->category_class }}</div>
                        <h4 class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Q: {{ $savedQ->question_text }}</h4>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            @foreach($savedQ->options as $oIdx => $optText)
                                <div wire:key="db-item-opt-{{ $savedQ->id }}-{{ $oIdx }}" class="p-2 rounded-lg text-[11px] border {{ $savedQ->correct_option_index === $oIdx ? 'bg-emerald-50 text-emerald-700 border-emerald-300 font-bold' : 'bg-white text-zinc-600 border-zinc-200' }}">
                                    {{ $oIdx === 0 ? 'A' : ($oIdx === 1 ? 'B' : ($oIdx === 2 ? 'C' : 'D')) }}: {{ $optText }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
