<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterDefaultValueInTweetLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tweet_logs', static function (Blueprint $table): void {
            $table->unsignedBigInteger('retweet_count')->default(0)->change();
            $table->unsignedBigInteger('reply_count')->default(0)->change();
            $table->unsignedBigInteger('like_count')->default(0)->change();
            $table->unsignedBigInteger('quote_count')->default(0)->change();
        });

        DB::statement("ALTER TABLE `tweet_logs`
            CHANGE COLUMN `impression_count` `impression_count` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `quote_count`,
            CHANGE COLUMN `url_link_clicks` `url_link_clicks` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `impression_count`,
            CHANGE COLUMN `user_profile_clicks` `user_profile_clicks` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `url_link_clicks`;
        ");

        DB::statement('ALTER TABLE `tweet_log_summaries`
            CHANGE COLUMN `created_at` `created_at` TIMESTAMP NULL DEFAULT NULL AFTER `total_user_profile_clicks`,
            CHANGE COLUMN `updated_at` `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tweet_logs', static function (Blueprint $table): void {
            $table->unsignedBigInteger('retweet_count')->change();
            $table->unsignedBigInteger('reply_count')->change();
            $table->unsignedBigInteger('like_count')->change();
            $table->unsignedBigInteger('quote_count')->change();
        });
    }
}
