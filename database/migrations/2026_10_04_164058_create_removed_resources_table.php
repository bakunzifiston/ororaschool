<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('removed_resources', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['platform', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('removed_resources');
    }
};
