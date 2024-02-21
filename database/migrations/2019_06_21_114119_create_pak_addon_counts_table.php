<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePakAddonCountsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pak_addon_counts', static function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->string('pak_slug', 255);
            $blueprint->string('addon_slug', 255);
            $blueprint->unsignedBigInteger('count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pak_addon_counts');
    }
}
