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
        Schema::dropIfExists('screenshots');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('screenshots', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->foreignId('user_id');
            $blueprint->text('description');
            $blueprint->json('links');
            $blueprint->string('status');
            $blueprint->timestamps();
            $blueprint->timestamp('published_at');
        });
    }
};
