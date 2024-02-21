<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInTweetLogSummariesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tweet_log_summaries', static function (Blueprint $table): void {
            $table->unsignedBigInteger('total_impression_count')->default(0);
            $table->unsignedBigInteger('total_url_link_clicks')->default(0);
            $table->unsignedBigInteger('total_user_profile_clicks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tweet_log_summaries', static function (Blueprint $table): void {
            $table->dropColumn([
                'total_impression_count',
                'total_url_link_clicks',
                'total_user_profile_clicks',
            ]);
        });
    }
}
