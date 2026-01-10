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
        Schema::create('mylists', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->cascadeOnDelete();
            $blueprint->string('title', 120);
            $blueprint->text('note')->nullable();
            $blueprint->boolean('is_public')->default(false);
            $blueprint->string('slug', 160)->nullable()->unique();
            $blueprint->timestamps();

            // インデックス
            $blueprint->index('user_id');
            $blueprint->index('is_public');
            $blueprint->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mylists');
    }
};
