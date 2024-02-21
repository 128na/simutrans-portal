<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInTweetLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tweet_logs', static function (Blueprint $table): void {
            $table->unsignedBigInteger('impression_count')->default(0);
            $table->unsignedBigInteger('url_link_clicks')->default(0);
            $table->unsignedBigInteger('user_profile_clicks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tweet_logs', static function (Blueprint $table): void {
            $table->dropColumn([
                'impression_count',
                'url_link_clicks',
                'user_profile_clicks',
            ]);
        });
    }
}
