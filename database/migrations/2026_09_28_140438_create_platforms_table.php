<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('discipline');
            $table->string('tagline');
            $table->text('description');
            $table->string('steward');
            $table->string('region');
            $table->unsignedInteger('learner_count')->default(0);
            $table->unsignedInteger('instructor_count')->default(0);
            $table->string('status', 20)->index();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->date('joined_at');
            $table->unsignedTinyInteger('completion_rate')->default(0);
            $table->string('cover')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
