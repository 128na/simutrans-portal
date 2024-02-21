<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('rankings', static function (Blueprint $table): void {
            $table->unsignedInteger('rank')->primary();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
}
