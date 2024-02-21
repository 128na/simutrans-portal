<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTagTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_tag', static function (Blueprint $blueprint): void {
            $blueprint->unsignedBigInteger('article_id');
            $blueprint->unsignedBigInteger('tag_id');
            $blueprint->index(['article_id', 'tag_id']);
            $blueprint->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
            $blueprint->foreign('tag_id')
                ->references('id')->on('tags')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_tag', static function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['article_id']);
            $blueprint->dropForeign(['tag_id']);
            $blueprint->dropIndex(['article_id', 'tag_id']);
        });

        Schema::dropIfExists('article_tag');
    }
}
