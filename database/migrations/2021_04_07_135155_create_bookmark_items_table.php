<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookmarkItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookmark_items', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('bookmark_id')->constrained()->onDelete('cascade');
            $blueprint->morphs('bookmark_itemable');
            $blueprint->text('memo')->nullable()->comment('メモ');
            $blueprint->unsignedInteger('order')->default(0)->comment('表示順');
            $blueprint->timestamps();
            $blueprint->index(['bookmark_id', 'order', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmark_items');
    }
}
