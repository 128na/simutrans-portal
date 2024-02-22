<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTweetLogSummariesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tweet_log_summaries', function (Blueprint $blueprint): void {
            $blueprint->unsignedBigInteger('article_id')->primary();
            $blueprint->unsignedBigInteger('total_retweet_count')->default(0);
            $blueprint->unsignedBigInteger('total_reply_count')->default(0);
            $blueprint->unsignedBigInteger('total_like_count')->default(0);
            $blueprint->unsignedBigInteger('total_quote_count')->default(0);
            $blueprint->timestamps();
            $blueprint->foreign('article_id')->references('id')->on('articles')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweet_log_summaries');
    }
}
