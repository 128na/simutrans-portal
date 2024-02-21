<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthCodesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oauth_tokens', static function (Blueprint $blueprint): void {
            $blueprint->string('application')->primary();
            $blueprint->string('token_type');
            $blueprint->string('scope');
            $blueprint->string('access_token');
            $blueprint->string('refresh_token');
            $blueprint->timestamp('expired_at');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_tokens');
    }
}
