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
        Schema::create('x_digest_logs', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->timestamp('cutoff_at');
            $blueprint->unsignedInteger('article_count')->default(0);
            $blueprint->timestamps();

            $blueprint->index('cutoff_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_digest_logs');
    }
};
