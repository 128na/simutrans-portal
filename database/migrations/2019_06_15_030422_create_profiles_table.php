<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザープロフィール
 */
class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', static function (Blueprint $blueprint) : void {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('user_id')->unique();
            $blueprint->json('data')->comment('プロフィール情報');
            $blueprint->timestamps();
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
        Schema::table('profiles', static function (Blueprint $blueprint) : void {
            $blueprint->dropForeign(['user_id']);
        });
        Schema::dropIfExists('profiles');
    }
}
