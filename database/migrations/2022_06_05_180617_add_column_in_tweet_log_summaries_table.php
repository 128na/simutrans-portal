<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInTweetLogSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tweet_log_summaries', function (Blueprint $table) {
            $table->unsignedBigInteger('total_impression_count')->default(0);
            $table->unsignedBigInteger('total_url_link_clicks')->default(0);
            $table->unsignedBigInteger('total_user_profile_clicks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tweet_log_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'total_impression_count',
                'total_url_link_clicks',
                'total_user_profile_clicks',
            ]);
        });
    }
}
