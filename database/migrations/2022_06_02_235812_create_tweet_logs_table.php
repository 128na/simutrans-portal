<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTweetLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tweet_logs', static function (Blueprint $blueprint) : void {
            $blueprint->string('id')->primary();
            $blueprint->foreignId('article_id')->constrained()->onDelete('cascade');
            $blueprint->text('text');
            $blueprint->unsignedBigInteger('retweet_count');
            $blueprint->unsignedBigInteger('reply_count');
            $blueprint->unsignedBigInteger('like_count');
            $blueprint->unsignedBigInteger('quote_count');
            $blueprint->timestamp('tweet_created_at');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweet_logs');
    }
}
