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
        Schema::create('conversions', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('article_id');
            $table->timestamps();
            $table->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', static function (Blueprint $table): void {
            $table->dropForeign(['article_id']);
        });
        Schema::dropIfExists('conversions');
    }
}
