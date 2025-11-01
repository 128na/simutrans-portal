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
        Schema::table('view_counts', function (Blueprint $blueprint): void {
            $blueprint->unsignedBigInteger('user_id');
            $blueprint->index(['user_id', 'type', 'period', 'count']);
            $blueprint->index(['article_id', 'type', 'period', 'count']);
            $blueprint->index(['type', 'period', 'article_id', 'count']);
            $blueprint->dropUnique(['article_id', 'type', 'period']);
            $blueprint->dropUnique(['type', 'period', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('view_counts', function (Blueprint $blueprint): void {
            $blueprint->unique(['article_id', 'type', 'period']);
            $blueprint->unique(['type', 'period', 'article_id']);
            $blueprint->dropIndex(['user_id', 'type', 'period', 'count']);
            $blueprint->dropIndex(['article_id', 'type', 'period', 'count']);
            $blueprint->dropIndex(['type', 'period', 'article_id', 'count']);
            $blueprint->dropColumn('user_id');
        });
    }
};
