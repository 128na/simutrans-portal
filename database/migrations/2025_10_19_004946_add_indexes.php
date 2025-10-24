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
        Schema::table('article_category', function (Blueprint $blueprint): void {
            $blueprint->unique(['category_id', 'article_id']);
            $blueprint->unique(['article_id', 'category_id']);
            $blueprint->dropIndex(['article_id', 'category_id']);
        });
        Schema::table('view_counts', function (Blueprint $blueprint): void {
            $blueprint->unique(['type', 'period', 'article_id']);
        });
        Schema::table('conversion_counts', function (Blueprint $blueprint): void {
            $blueprint->unique(['type', 'period', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_category', function (Blueprint $blueprint): void {
            $blueprint->index(['article_id', 'category_id']);
            $blueprint->dropUnique(['article_id', 'category_id']);
            $blueprint->dropUnique(['category_id', 'article_id']);
        });
        Schema::table('view_counts', function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['type', 'period', 'article_id']);
        });
        Schema::table('conversion_counts', function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['type', 'period', 'article_id']);
        });
    }
};
