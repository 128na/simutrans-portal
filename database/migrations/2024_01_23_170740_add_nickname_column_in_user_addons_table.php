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
        Schema::table('user_addon_counts', static function (Blueprint $table) {
            $table->string('user_nickname')->nullable()->after('user_name')->comment('表示名');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addon_counts', static function (Blueprint $table) {
            $table->dropColumn('user_nickname');
        });
    }
};
