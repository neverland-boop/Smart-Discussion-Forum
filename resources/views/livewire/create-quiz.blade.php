<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

// 1. Tell Livewire to wrap this page inside your universal layout frame
new #[Layout('components.layouts.app')] class extends Component {
    public string $quizTitle = '';
    public string $categoryClass = '';
    public string $date = '';
    public string $time = '';
    public string $duration = '';
    public string $totalMarks = '';
    public string $instructions = '';

    public function saveDraft(): void
    {
        session()->flash('status', 'Quiz saved as draft successfully.');
    }

    public function publishQuiz(): void
    {
        $this->validate([
            'quizTitle' => 'required|string|max:255',
            'categoryClass' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer',
            'totalMarks' => 'required|integer',
        ]);

        session()->flash('status', 'Quiz published successfully.');
    }
}; ?>

<!-- 2. The sidebar layout container is gone. Only your specific right-side form content belongs here! -->
<div class="flex flex-col justify-between h-full">
    <div>
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-zinc-800 tracking-tight">Create Quiz</h1>
            <!-- Breadcrumbs -->
            <nav class="text-xs font-semibold text-zinc-400 mt-1 flex items-center gap-1.5">
                <a href="#" class="hover:underline">Home</a> <span>›</span>
                <a href="#" class="hover:underline">Quizzes</a> <span>›</span>
                <span class="text-zinc-600 font-bold">Create Quiz</span>
            </nav>
        </div>

        <!-- Validation/Success Feedback -->
        @if (session()->has('status'))
            <div class="mb-4 text-xs font-semibold bg-emerald-50 text-emerald-700 p-3 rounded-lg border border-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <!-- Two-Column Grid Inputs Layout -->
        <form id="quizForm" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <!-- Quiz Title -->
            <div class="flex flex-col gap-1.5">
                <label for="quizTitle" class="text-xs font-bold text-zinc-700">Quiz Title</label>
                <input wire:model="quizTitle" id="quizTitle" type="text" placeholder="Enter quiz title" class="w-full bg-white text-xs border border-zinc-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm" />
            </div>

            <!-- Category/Class Selection Dropdown -->
            <div class="flex flex-col gap-1.5">
                <label for="categoryClass" class="text-xs font-bold text-zinc-700">Category/Class</label>
                <select wire:model="categoryClass" id="categoryClass" class="w-full bg-white text-xs border border-zinc-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm text-zinc-500">
                    <option value="">Select category/class</option>
                    <option value="cs101">Computer Science 101</option>
                    <option value="se202">Software Engineering II</option>
                </select>
            </div>

            <!-- Date -->
            <div class="flex flex-col gap-1.5">
                <label for="date" class="text-xs font-bold text-zinc-700">Date</label>
                <input wire:model="date" id="date" type="date" class="w-full bg-white text-xs border border-zinc-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm text-zinc-500" />
            </div>

            <!-- Time -->
            <div class="flex flex-col gap-1.5">
                <label for="time" class="text-xs font-bold text-zinc-700">Time</label>
                <input wire:model="time" id="time" type="time" class="w-full bg-white text-xs border border-zinc-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm text-zinc-500" />
            </div>

            <!-- Duration (minutes) -->
            <div class="flex flex-col gap-1.5">
                <label for="duration" class="text-xs font-bold text-zinc-700">Duration (minutes)</label>
                <input wire:model="duration" id="duration" type="number" placeholder="Enter duration" class="w-full bg-white text-xs border border-zinc-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm" />
            </div>

            <!-- Total Marks -->
            <div class="flex flex-col gap-1.5">
                <label for="totalMarks" class="text-xs font-bold text-zinc-700">Total Marks</label>
                <input wire:model="totalMarks" id="totalMarks" type="number" placeholder="Enter total marks" class="w-full bg-white text-xs border border-zinc-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm" />
            </div>

            <!-- Full-Width Field: Instructions for Students -->
            <div class="col-span-1 md:col-span-2 flex flex-col gap-1.5 mt-2">
                <label for="instructions" class="text-xs font-bold text-zinc-700">Instructions for Students (Optional)</label>
                <textarea wire:model="instructions" id="instructions" placeholder="Enter instructions for the quiz..." class="w-full bg-white text-xs border border-zinc-200 rounded-lg px-3 py-2.5 h-24 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm"></textarea>
            </div>
        </form>
    </div>

    <!-- FOOTER BUTTONS ACTION BAR -->
    <div class="mt-8 flex justify-end gap-4 border-t border-zinc-100 pt-4">
        <button wire:click="saveDraft" class="bg-white hover:bg-zinc-50 text-zinc-700 border border-zinc-300 font-bold text-xs px-6 py-2.5 rounded-xl shadow-sm transition-all cursor-pointer">
            Save as Draft
        </button>
        <button wire:click="publishQuiz" type="button" class="bg-[#10b981] hover:bg-[#0fd693] text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer border-none">
            <span>✈️</span> Publish Quiz
        </button>
    </div>
</div>
