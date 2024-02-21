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
        Schema::table('views', static function (Blueprint $table): void {
            $table->dropForeign(['article_id']);
        });
        Schema::dropIfExists('views');

        Schema::create('view_counts', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('type')->comment('集計区分 1:日次,2:月次,3:年次,4:全体');
            $table->string('period')->comment('集計期間');
            $table->unsignedBigInteger('count')->default(0)->comment('カウント');
            $table->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
            $table->unique(['article_id', 'type', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('view_counts', static function (Blueprint $table): void {
            $table->dropForeign(['article_id']);
            $table->dropUnique(['article_id', 'type', 'period']);
        });
        Schema::dropIfExists('view_counts');

        Schema::create('views', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('article_id');
            $table->timestamps();
            $table->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
        });
    }
}
