<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * views を削除してview_countsを作成する
 */
class CreateViewCountsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('views', function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['article_id']);
        });
        Schema::dropIfExists('views');

        Schema::create('view_counts', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('article_id');
            $blueprint->unsignedInteger('type')->comment('集計区分 1:日次,2:月次,3:年次,4:全体');
            $blueprint->string('period')->comment('集計期間');
            $blueprint->unsignedBigInteger('count')->default(0)->comment('カウント');
            $blueprint->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
            $blueprint->unique(['article_id', 'type', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('view_counts', function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['article_id']);
            $blueprint->dropUnique(['article_id', 'type', 'period']);
        });
        Schema::dropIfExists('view_counts');

        Schema::create('views', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('article_id');
            $blueprint->timestamps();
            $blueprint->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
        });
    }
}
