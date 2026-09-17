<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Models\Article;
use Tests\Feature\TestCase;

/**
 * FallbackShowAction (/articles/{id}) のリダイレクト先URLが日本語スラッグでも
 * 二重エンコードされないことを確認する回帰テスト。
 *
 * NOTE: 既存の ShowControllerTest.php は変更禁止のため、別ファイルとして追加している。
 */
class FallbackShowUrlRegressionTest extends TestCase
{
    public function test_日本語スラッグのfallback_showリダイレクト先urlが二重エンコードされない(): void
    {
        $article = Article::factory()->publish()->create([
            'title' => '日本語のフォールバック記事',
            'slug' => '日本語のフォールバック記事',
        ]);

        $testResponse = $this->get('/articles/'.$article->slug);

        $testResponse->assertRedirect($article->showUrl());
        $this->assertStringNotContainsString('%25', $article->showUrl());
    }
}
