<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBookmarkTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('bookmark_items');
        Schema::dropIfExists('bookmarks');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('bookmarks', static function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('is_public')->default(0)->comment('公開ステータス(0:非公開、1:公開)');
            $table->string('title')->comment('ブックマーク名');
            $table->text('description')->nullable()->comment('説明');
            $table->timestamps();
            $table->index('title');
            $table->index(['is_public', 'updated_at']);
        });
        Schema::create('bookmark_items', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookmark_id')->constrained()->onDelete('cascade');
            $table->morphs('bookmark_itemable');
            $table->text('memo')->nullable()->comment('メモ');
            $table->unsignedInteger('order')->default(0)->comment('表示順');
            $table->timestamps();
            $table->index(['bookmark_id', 'order', 'created_at']);
        });
    }
}
