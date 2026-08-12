<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_tokens', function (Blueprint $table) {
            $table->text('access_token')->change();
            $table->text('refresh_token')->change();
        });
    }

    public function down(): void
    {
        Schema::table('oauth_tokens', function (Blueprint $table) {
            $table->string('access_token')->change();
            $table->string('refresh_token')->change();
        });
    }
};
