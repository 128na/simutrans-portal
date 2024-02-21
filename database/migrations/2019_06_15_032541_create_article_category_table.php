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
        Schema::create('article_category', static function (Blueprint $table): void {
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('category_id');
            $table->index(['article_id', 'category_id']);
            $table->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_category', static function (Blueprint $table): void {
            $table->dropForeign(['article_id']);
            $table->dropForeign(['category_id']);
            $table->dropIndex(['article_id', 'category_id']);
        });

        Schema::dropIfExists('article_category');
    }
}
