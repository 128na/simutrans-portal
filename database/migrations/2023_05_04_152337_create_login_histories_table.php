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
        Schema::create('login_histories', static function (Blueprint $blueprint) : void {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('ip')->nullable();
            $blueprint->string('ua')->nullable();
            $blueprint->string('referer')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
