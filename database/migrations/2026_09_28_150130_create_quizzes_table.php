<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('lesson_title');
            $table->unsignedTinyInteger('question_count')->default(0);
            $table->unsignedTinyInteger('pass_score')->default(70);
            $table->unsignedTinyInteger('attempt_limit')->default(3);
            $table->unsignedTinyInteger('average_score')->nullable();
            $table->json('items');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
