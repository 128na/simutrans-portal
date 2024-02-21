<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversions', static function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('article_id');
            $blueprint->timestamps();
            $blueprint->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', static function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['article_id']);
        });
        Schema::dropIfExists('conversions');
    }
}
