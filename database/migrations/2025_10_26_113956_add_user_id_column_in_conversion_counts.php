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
        Schema::table('conversion_counts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->index(['user_id', 'type', 'period', 'count']);
            $table->index(['article_id', 'type', 'period', 'count']);
            $table->index(['type', 'period', 'article_id', 'count']);
            $table->dropUnique(['article_id', 'type', 'period']);
            $table->dropUnique(['type', 'period', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('conversion_counts', function (Blueprint $table) {
            $table->unique(['article_id', 'type', 'period']);
            $table->unique(['type', 'period', 'article_id']);
            $table->dropIndex(['user_id', 'type', 'period', 'count']);
            $table->dropIndex(['article_id', 'type', 'period', 'count']);
            $table->dropIndex(['type', 'period', 'article_id', 'count']);
            $table->dropColumn('user_id');
        });
    }
};
