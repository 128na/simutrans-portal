<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_screenshot', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('article_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('screenshot_id')->constrained()->onDelete('cascade');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_screenshot');
    }
};
