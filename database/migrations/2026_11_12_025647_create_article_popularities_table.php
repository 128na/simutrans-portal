<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 一覧の「人気順」の並び（#683）。人気は記事本体の情報ではないので articles とは分けて持つ
 *
 * article:refresh-article-popularities が毎時、全記事分を作り直す（前日までの直近 90 日の CV 合計。0 も含む）。
 * rank は CV 合計・公開日時・id の降順で振った通し番号で、一覧はこの順に並べる。記事を物理削除したら行も消える。
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_popularities', function (Blueprint $blueprint): void {
            $blueprint->foreignId('article_id')->primary()->constrained()->cascadeOnDelete();
            $blueprint->unsignedInteger('rank')->unique()->comment('人気順の順位（1 から。CV 合計・公開日時・id の降順）');
            $blueprint->unsignedInteger('conversion_count')->comment('前日までの直近90日のCV合計');
        });

        // 最初の集計までの仮の順位。空のままだと人気順の一覧が 0 件になるので、CV を 0 として公開が新しい順に振る。
        // conversion_counts は読まない（CV の記録を待たせない）。実際の順位は article:refresh-article-popularities で入る
        DB::statement(
            'INSERT INTO article_popularities (article_id, `rank`, conversion_count)'
            .' SELECT id, ROW_NUMBER() OVER (ORDER BY published_at DESC, id DESC), 0 FROM articles'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_popularities');
    }
};
