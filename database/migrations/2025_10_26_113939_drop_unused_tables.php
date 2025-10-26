<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('rankings');
        Schema::dropIfExists('pak_addon_counts');
        Schema::dropIfExists('tweet_log_summaries');
        Schema::dropIfExists('tweet_logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
            CREATE TABLE `rankings` (
            `rank` int unsigned NOT NULL,
            `article_id` bigint unsigned NOT NULL,
            PRIMARY KEY (`rank`),
            KEY `rankings_article_id_foreign` (`article_id`),
            CONSTRAINT `rankings_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        */
        Schema::create('rankings', function (Blueprint $table): void {
            $table->increments('rank');
            $table->unsignedBigInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });

        /*
            CREATE TABLE `pak_addon_counts` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint unsigned NOT NULL,
            `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `user_nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `count` int unsigned NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        */
        Schema::create('pak_addon_counts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_nickname');
            $table->unsignedInteger('count');
        });

        /*
            CREATE TABLE `tweet_log_summaries` (
            `article_id` bigint unsigned NOT NULL,
            `total_retweet_count` bigint unsigned NOT NULL DEFAULT '0',
            `total_reply_count` bigint unsigned NOT NULL DEFAULT '0',
            `total_like_count` bigint unsigned NOT NULL DEFAULT '0',
            `total_quote_count` bigint unsigned NOT NULL DEFAULT '0',
            `total_impression_count` bigint unsigned NOT NULL DEFAULT '0',
            `total_url_link_clicks` bigint unsigned NOT NULL DEFAULT '0',
            `total_user_profile_clicks` bigint unsigned NOT NULL DEFAULT '0',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`article_id`),
            CONSTRAINT `tweet_log_summaries_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        */
        Schema::create('tweet_log_summaries', function (Blueprint $table): void {
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('total_retweet_count');
            $table->unsignedBigInteger('total_reply_count');
            $table->unsignedBigInteger('total_like_count');
            $table->unsignedBigInteger('total_quote_count');
            $table->unsignedBigInteger('total_impression_count');
            $table->unsignedBigInteger('total_url_link_clicks');
            $table->unsignedBigInteger('total_user_profile_clicks');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });

        /*
        CREATE TABLE `tweet_logs` (
        `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        `article_id` bigint unsigned NOT NULL,
        `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        `retweet_count` bigint unsigned NOT NULL DEFAULT '0',
        `reply_count` bigint unsigned NOT NULL DEFAULT '0',
        `like_count` bigint unsigned NOT NULL DEFAULT '0',
        `quote_count` bigint unsigned NOT NULL DEFAULT '0',
        `impression_count` bigint unsigned NOT NULL DEFAULT '0',
        `url_link_clicks` bigint unsigned NOT NULL DEFAULT '0',
        `user_profile_clicks` bigint unsigned NOT NULL DEFAULT '0',
        `tweet_created_at` timestamp NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `tweet_logs_article_id_foreign` (`article_id`),
        CONSTRAINT `tweet_logs_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        */
        Schema::create('tweet_logs', function (Blueprint $table): void {
            $table->string('id');
            $table->unsignedBigInteger('article_id');
            $table->text('text');
            $table->unsignedBigInteger('retweet_count');
            $table->unsignedBigInteger('reply_count');
            $table->unsignedBigInteger('like_count');
            $table->unsignedBigInteger('quote_count');
            $table->unsignedBigInteger('impression_count');
            $table->unsignedBigInteger('url_link_clicks');
            $table->unsignedBigInteger('user_profile_clicks');
            $table->timestamp('tweet_created_at');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }
};
