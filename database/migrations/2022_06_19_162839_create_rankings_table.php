<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingsTable extends Migration
{
    public function up()
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->unsignedInteger('rank')->primary();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rankings');
    }
}
