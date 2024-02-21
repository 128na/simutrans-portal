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
        Schema::table('users', static function (Blueprint $blueprint) : void {
            $blueprint->string('nickname')->unique()->nullable()->after('name')->comment('表示名');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $blueprint) : void {
            $blueprint->dropColumn('nickname');
        });
    }
};
