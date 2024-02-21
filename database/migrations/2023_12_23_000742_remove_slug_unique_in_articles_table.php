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
        Schema::table('articles', static function (Blueprint $blueprint) : void {
            $blueprint->dropUnique(['slug']);
            $blueprint->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', static function (Blueprint $blueprint) : void {
            $blueprint->dropIndex(['slug']);
            $blueprint->unique(['slug']);
        });
    }
};
