<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('status', 30);
            $table->timestamps();

            $table->unique(['platform_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academies');
    }
};
