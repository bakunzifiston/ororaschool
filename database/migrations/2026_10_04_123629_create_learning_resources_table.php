<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('type');
            $table->string('attached_kind');
            $table->string('attached_to');
            $table->string('attached_key');
            $table->string('size')->default('—');
            $table->string('path')->nullable();
            $table->timestamps();

            $table->unique(['platform_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_resources');
    }
};
