<?php

declare(strict_types=1);

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
        Schema::table('articles', function (Blueprint $blueprint): void {
            $blueprint->unique(['user_id', 'slug']);
            $blueprint->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['user_id', 'slug']);
            $blueprint->dropIndex(['deleted_at']);
        });
    }
};
