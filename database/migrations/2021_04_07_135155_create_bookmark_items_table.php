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
        Schema::create('bookmark_items', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bookmark_id')->constrained()->onDelete('cascade');
            $table->morphs('bookmark_itemable');
            $table->text('memo')->nullable()->comment('メモ');
            $table->unsignedInteger('order')->default(0)->comment('表示順');
            $table->timestamps();
            $table->index(['bookmark_id', 'order', 'created_at']);
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
