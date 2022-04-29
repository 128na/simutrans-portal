<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvitedByColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('invited_by')->nullable()->comment('紹介ユーザーID');
            $table->index('invited_by');

            $table->uuid('invitation_code')->nullable()->comment('紹介用コード');
            $table->unique('invitation_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['invited_by']);
            $table->dropColumn('invited_by');

            $table->dropUnique(['invitation_code']);
            $table->dropColumn('invitation_code');
        });
    }
}
