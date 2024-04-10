<?php

declare(strict_types=1);

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
        Schema::dropIfExists('user_addon_counts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('user_addon_counts', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->foreignId('user_id');
            $blueprint->string('user_name');
            $blueprint->string('user_nickname');
            $blueprint->unsignedInteger('count');
        });
    }
};
