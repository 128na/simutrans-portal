<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookmarkItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookmark_items', function (Blueprint $table) {
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookmark_items');
    }
}
