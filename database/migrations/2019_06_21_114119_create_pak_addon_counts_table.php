<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePakAddonCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pak_addon_counts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pak_slug', 255);
            $table->string('addon_slug', 255);
            $table->unsignedBigInteger('count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pak_addon_counts');
    }
}
