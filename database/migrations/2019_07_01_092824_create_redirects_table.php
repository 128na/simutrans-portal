<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('redirects', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('from')->comment('リダイレクト元');
            $table->string('to')->comment('リダイレクト先');
            $table->timestamps();
            $table->unique(['from', 'to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('redirects', static function (Blueprint $table): void {
            $table->dropUnique(['from', 'to']);
        });

        Schema::dropIfExists('redirects');
    }
}
