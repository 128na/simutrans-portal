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
        Schema::table('conversion_counts', function (Blueprint $blueprint): void {
            if (!Schema::hasIndex('conversion_counts', 'uk_for_on_duplicate_key_update')) {
                $blueprint->unique(['article_id', 'type', 'period'], 'uk_for_on_duplicate_key_update');
            }
        });
        Schema::table('view_counts', function (Blueprint $blueprint): void {
            if (!Schema::hasIndex('view_counts', 'uk_for_on_duplicate_key_update')) {
                $blueprint->unique(['article_id', 'type', 'period'], 'uk_for_on_duplicate_key_update');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversion_counts', function (Blueprint $blueprint): void {
            $blueprint->dropUnique('uk_for_on_duplicate_key_update');
        });
        Schema::table('view_counts', function (Blueprint $blueprint): void {
            $blueprint->dropUnique('uk_for_on_duplicate_key_update');
        });
    }
};
