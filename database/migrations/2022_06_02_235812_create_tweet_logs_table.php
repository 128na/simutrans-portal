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
        Schema::create('tweet_logs', static function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->text('text');
            $table->unsignedBigInteger('retweet_count');
            $table->unsignedBigInteger('reply_count');
            $table->unsignedBigInteger('like_count');
            $table->unsignedBigInteger('quote_count');
            $table->timestamp('tweet_created_at');
            $table->timestamps();
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
