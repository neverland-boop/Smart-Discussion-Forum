<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Quiz::create([
            'title' => 'Data Structures Quiz',
            'description' => 'A quiz on linear and non-linear structures.',
            'status' => 'ANNOUNCED',
            'creator_id' => 1, // Assumes user ID 1 exists
            'group_id' => 1,   // Assumes group ID 1 exists
            'time_limit' => 30,
            'auto_submit' => true,
            'start_time' => now()->addDay(),
        ]);
    }
}
