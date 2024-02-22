<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishedAtColumnInArticlesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $blueprint): void {
            $blueprint->timestamp('published_at')->nullable()->after('status')->comment('投稿日時');
            $blueprint->index(['published_at']);
            $blueprint->index(['status', 'published_at']);
            $blueprint->timestamp('modified_at')->nullable()->after('published_at')->comment('更新日時');
            $blueprint->index(['modified_at']);
            $blueprint->index(['status', 'modified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $blueprint): void {
            $blueprint->dropIndex(['published_at']);
            $blueprint->dropIndex(['status', 'published_at']);
            $blueprint->dropColumn('published_at');
            $blueprint->dropIndex(['modified_at']);
            $blueprint->dropIndex(['status', 'modified_at']);
            $blueprint->dropColumn('modified_at');
        });
    }
}
