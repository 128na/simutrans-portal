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
        Schema::table('attachments', function (Blueprint $blueprint): void {
            $blueprint->string('caption')->nullable()->comment('キャプション（画像向け）');
            $blueprint->unsignedInteger('order')->default(0)->comment('表示順（画像向け）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $blueprint): void {
            //
        });
    }
};
