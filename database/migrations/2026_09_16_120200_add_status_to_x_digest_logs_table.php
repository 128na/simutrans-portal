<?php

declare(strict_types=1);

use App\Enums\XDigestLogStatus;
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
        Schema::table('x_digest_logs', function (Blueprint $blueprint): void {
            $blueprint->string('status')->default(XDigestLogStatus::Success->value)->after('article_count');
            $blueprint->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('x_digest_logs', function (Blueprint $blueprint): void {
            $blueprint->dropColumn('status');
        });
    }
};
