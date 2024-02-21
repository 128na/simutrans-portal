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
        Schema::create('articles', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255)->comment('タイトル');
            $table->string('slug', 255)->unique()->comment('スラッグ');
            $table->string('post_type', 255)->comment('投稿形式');
            $table->json('contents')->comment('コンテンツ');
            $table->string('status', 255)->comment('公開状態');
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', static function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('articles');
    }
}
