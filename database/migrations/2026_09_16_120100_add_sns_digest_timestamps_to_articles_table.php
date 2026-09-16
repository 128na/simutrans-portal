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
            $blueprint->timestamp('sns_digest_published_at')->nullable()->after('modified_at');
            $blueprint->timestamp('sns_digest_updated_at')->nullable()->after('sns_digest_published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $blueprint): void {
            $blueprint->dropColumn(['sns_digest_published_at', 'sns_digest_updated_at']);
        });
    }
};
