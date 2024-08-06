<?php

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
        Schema::dropIfExists('screenshots');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('screenshots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id');
            $table->text('description');
            $table->json('links');
            $table->string('status');
            $table->timestamps();
            $table->timestamp('published_at');
        });
    }
};
