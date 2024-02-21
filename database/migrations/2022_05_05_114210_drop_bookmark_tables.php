<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBookmarkTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('bookmark_items');
        Schema::dropIfExists('bookmarks');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('bookmarks', static function (Blueprint $blueprint) : void {
            $blueprint->id();
            $blueprint->uuid('uuid')->unique();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->unsignedTinyInteger('is_public')->default(0)->comment('公開ステータス(0:非公開、1:公開)');
            $blueprint->string('title')->comment('ブックマーク名');
            $blueprint->text('description')->nullable()->comment('説明');
            $blueprint->timestamps();
            $blueprint->index('title');
            $blueprint->index(['is_public', 'updated_at']);
        });
        Schema::create('bookmark_items', static function (Blueprint $blueprint) : void {
            $blueprint->id();
            $blueprint->foreignId('bookmark_id')->constrained()->onDelete('cascade');
            $blueprint->morphs('bookmark_itemable');
            $blueprint->text('memo')->nullable()->comment('メモ');
            $blueprint->unsignedInteger('order')->default(0)->comment('表示順');
            $blueprint->timestamps();
            $blueprint->index(['bookmark_id', 'order', 'created_at']);
        });
    }
}
