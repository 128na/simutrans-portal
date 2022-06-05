<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInTweetLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tweet_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('impression_count')->default(0);
            $table->unsignedBigInteger('url_link_clicks')->default(0);
            $table->unsignedBigInteger('user_profile_clicks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tweet_logs', function (Blueprint $table) {
            $table->dropColumn([
                'impression_count',
                'url_link_clicks',
                'user_profile_clicks',
            ]);
        });
    }
}
