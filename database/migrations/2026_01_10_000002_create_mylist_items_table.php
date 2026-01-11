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
        Schema::create('mylist_items', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('list_id')->constrained('mylists')->cascadeOnDelete();
            $blueprint->foreignId('article_id')->constrained()->cascadeOnDelete();
            $blueprint->string('note', 255)->nullable();
            $blueprint->unsignedInteger('position')->nullable();
            $blueprint->timestamps();

            // ユニーク制約
            $blueprint->unique(['list_id', 'article_id']);

            // インデックス
            $blueprint->index('list_id');
            $blueprint->index('article_id');
            $blueprint->index('position');
            $blueprint->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mylist_items');
    }
};
