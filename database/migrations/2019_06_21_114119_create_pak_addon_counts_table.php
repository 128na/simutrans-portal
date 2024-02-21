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
        Schema::create('pak_addon_counts', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('pak_slug', 255);
            $table->string('addon_slug', 255);
            $table->unsignedBigInteger('count');
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
