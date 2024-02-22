<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 記事
 */
class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('user_id');
            $blueprint->string('title', 255)->comment('タイトル');
            $blueprint->string('slug', 255)->unique()->comment('スラッグ');
            $blueprint->string('post_type', 255)->comment('投稿形式');
            $blueprint->json('contents')->comment('コンテンツ');
            $blueprint->string('status', 255)->comment('公開状態');
            $blueprint->timestamps();
            $blueprint->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['user_id']);
        });
        Schema::dropIfExists('articles');
    }
}
