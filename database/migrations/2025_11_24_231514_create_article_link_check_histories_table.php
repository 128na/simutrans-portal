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
        Schema::create('article_link_check_histories', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('article_id')->constrained()->cascadeOnDelete();
            $blueprint->unsignedInteger('failed_count')->default(0);
            $blueprint->timestamp('last_checked_at')->useCurrent();
            $blueprint->timestamps();

            $blueprint->unique('article_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_link_check_histories');
    }
};
