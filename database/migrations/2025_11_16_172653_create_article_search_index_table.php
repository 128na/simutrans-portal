<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_search_index', function (Blueprint $blueprint): void {
            $blueprint->unsignedBigInteger('article_id')->primary();
            $blueprint->longText('text')->nullable();
            $blueprint->timestamps();
        });

        // FULLTEXT INDEX（NGRAM パーサー付き）を追加
        DB::statement(
            'ALTER TABLE article_search_index
             ADD FULLTEXT INDEX article_search_index_text_fulltext (text)
             WITH PARSER ngram'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('article_search_index');
    }
};
