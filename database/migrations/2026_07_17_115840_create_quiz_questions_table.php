<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('quiz_title');
            $table->string('category_class');
            $table->text('question_text');
            $table->json('options'); // Stores options A, B, C, D as a clean array structure
            $table->integer('correct_option_index'); // Stores 0, 1, 2, or 3
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
