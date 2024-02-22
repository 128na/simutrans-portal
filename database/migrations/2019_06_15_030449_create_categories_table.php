<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->string('name', 255)->comment('カテゴリ名');
            $blueprint->string('type', 255)->comment('分類');
            $blueprint->string('slug', 255)->comment('スラッグ');
            $blueprint->unsignedTinyInteger('need_admin')->default(0)->comment('管理者専用カテゴリ');
            $blueprint->unsignedInteger('order')->default(0)->comment('表示順');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}
