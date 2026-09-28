<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academy_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary');
            $table->text('description');
            $table->string('instructor');
            $table->string('status', 30);
            $table->string('difficulty', 30);
            $table->string('language');
            $table->string('category')->nullable();
            $table->unsignedSmallInteger('modules')->default(0);
            $table->unsignedSmallInteger('lessons')->default(0);
            $table->unsignedInteger('duration')->default(0);
            $table->unsignedInteger('enrolled')->default(0);
            $table->boolean('paid')->default(false);
            $table->boolean('certificate_eligible')->default(false);
            $table->boolean('enrollment_required')->default(true);
            $table->string('cover')->nullable();
            $table->date('content_updated_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'platform_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
