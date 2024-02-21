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
        Schema::create('categories', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name', 255)->comment('カテゴリ名');
            $table->string('type', 255)->comment('分類');
            $table->string('slug', 255)->comment('スラッグ');
            $table->unsignedTinyInteger('need_admin')->default(0)->comment('管理者専用カテゴリ');
            $table->unsignedInteger('order')->default(0)->comment('表示順');
            $table->timestamps();
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
