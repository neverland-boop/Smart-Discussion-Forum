<?php
use Livewire\Volt\Component;
use App\Models\Quiz;

new class extends Component {
    public function with(): array
    {
        return [
            'upcomingQuizzes' => Quiz::where('status', 'ANNOUNCED')->latest()->get(),
        ];
    }
}; ?>

<!-- Your HTML follows below -->
<div class="p-8 max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-white mb-6">Available Quizzes</h1>
    
    <div class="grid gap-4">
        @forelse($upcomingQuizzes as $quiz)
            <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl flex justify-between items-center">
                <h3 class="text-white">{{ $quiz->title }}</h3>
                <a href="{{ route('quiz.attempt', ['id' => $quiz->id]) }}" class="text-green-500">Attempt Quiz</a>
            </div>
        @empty
            <p class="text-slate-500">No quizzes scheduled.</p>
        @endforelse
    </div>
</div>