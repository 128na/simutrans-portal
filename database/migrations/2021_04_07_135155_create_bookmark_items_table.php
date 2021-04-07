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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('memo')->nullable()->comment('メモ');
            $table->timestamps();
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
