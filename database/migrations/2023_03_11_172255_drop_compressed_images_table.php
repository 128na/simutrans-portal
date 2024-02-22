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
        Schema::dropIfExists('compressed_images');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('compressed_images', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->string('path')->unique();
            $blueprint->timestamps();
        });
    }
};
