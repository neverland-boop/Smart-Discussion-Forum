<?php
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            // Requirement 10: Quiz configuration including time, date, duration, and category
            'pendingQuizzes' => [
                ['title' => 'Midterm Evaluation', 'category' => 'Year 2 Students', 'date' => '2026-07-15', 'time' => '14:00', 'duration' => '60 mins'],
            ],
            // Requirement 9: Awarding marks for participation
            'participationRecords' => [
                ['name' => 'Alice Johnson', 'posts' => 45, 'replies' => 12, 'quality' => 'High', 'marks' => 'Pending'],
                ['name' => 'Bob Smith', 'posts' => 3, 'replies' => 0, 'quality' => 'Low', 'marks' => 'Pending'],
            ]
        ];
    }
}; ?>

<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Quiz Configuration Hub -->
        <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-white">Quiz Management</h2>
                <button class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-bold py-2 px-4 rounded-lg transition shadow-lg text-sm">
                    + Configure New Quiz
                </button>
            </div>
            
            <div class="space-y-4">
                @foreach($pendingQuizzes as $quiz)
                <div class="p-4 border border-slate-700 bg-slate-900/50 rounded-xl flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $quiz['title'] }}</h3>
                        <p class="text-sm text-slate-400 mt-1">
                            Target: <span class="text-blue-400">{{ $quiz['category'] }}</span> | Duration: {{ $quiz['duration'] }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-white font-medium">{{ $quiz['date'] }}</p>
                        <p class="text-sm text-amber-400">{{ $quiz['time'] }} EAT</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 flex flex-col justify-center">
            <h3 class="text-slate-400 font-medium text-center">Recent Quiz Performance</h3>
            <p class="text-5xl font-extrabold text-emerald-400 text-center mt-4">78%</p>
            <p class="text-sm text-slate-500 text-center mt-2">Average score for last quiz</p>
            <button class="mt-6 w-full bg-slate-700 hover:bg-slate-600 text-white font-semibold py-2 rounded-lg transition">
                View Full Report
            </button>
        </div>
    </div>

    <!-- Participation Marks Grading -->
    <h2 class="text-2xl font-bold mb-6 text-white border-b border-slate-800 pb-4">Participation Evaluation</h2>
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-900/50 text-slate-400 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4">Student Name</th>
                    <th class="px-6 py-4 text-center">Total Posts</th>
                    <th class="px-6 py-4 text-center">Replies</th>
                    <th class="px-6 py-4">Quality Metric</th>
                    <th class="px-6 py-4 text-right">Award Marks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($participationRecords as $record)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 font-medium text-white">{{ $record['name'] }}</td>
                    <td class="px-6 py-4 text-center">{{ $record['posts'] }}</td>
                    <td class="px-6 py-4 text-center">{{ $record['replies'] }}</td>
                    <td class="px-6 py-4">
                        <span class="text-{{ $record['quality'] === 'High' ? 'emerald' : 'red' }}-400 font-medium">
                            {{ $record['quality'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <input type="number" placeholder="/ 10" class="w-20 bg-slate-900 border border-slate-600 rounded px-2 py-1 text-white text-right outline-none focus:border-purple-500">
                        <button class="ml-2 text-purple-400 hover:text-purple-300 font-semibold">Save</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>