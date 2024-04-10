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
        Schema::dropIfExists('pak_addon_counts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pak_addon_counts', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->string('pak_slug');
            $blueprint->string('addon_slug');
            $blueprint->unsignedInteger('count');
        });
    }
};
