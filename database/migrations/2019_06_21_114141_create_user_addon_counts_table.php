<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddonCountsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_addon_counts', static function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('user_id');
            $blueprint->string('user_name', 255);
            $blueprint->unsignedBigInteger('count');
            $blueprint->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addon_counts', static function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['user_id']);
        });

        Schema::dropIfExists('user_addon_counts');
    }
}
