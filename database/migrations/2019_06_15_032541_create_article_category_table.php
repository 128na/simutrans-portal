<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 記事ーカテゴリリレーション
 */
class CreateArticleCategoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_category', static function (Blueprint $blueprint): void {
            $blueprint->unsignedBigInteger('article_id');
            $blueprint->unsignedBigInteger('category_id');
            $blueprint->index(['article_id', 'category_id']);
            $blueprint->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
            $blueprint->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_category', static function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['article_id']);
            $blueprint->dropForeign(['category_id']);
            $blueprint->dropIndex(['article_id', 'category_id']);
        });

        Schema::dropIfExists('article_category');
    }
}
