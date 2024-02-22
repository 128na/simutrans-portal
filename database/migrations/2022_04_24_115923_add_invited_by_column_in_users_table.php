<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvitedByColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $blueprint): void {
            $blueprint->unsignedBigInteger('invited_by')->nullable()->comment('紹介ユーザーID');
            $blueprint->index('invited_by');
            $blueprint->uuid('invitation_code')->nullable()->comment('紹介用コード');
            $blueprint->unique('invitation_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint): void {
            $blueprint->dropIndex(['invited_by']);
            $blueprint->dropColumn('invited_by');
            $blueprint->dropUnique(['invitation_code']);
            $blueprint->dropColumn('invitation_code');
        });
    }
}
