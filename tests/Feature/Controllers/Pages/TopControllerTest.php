<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use Tests\Feature\TestCase;

class TopControllerTest extends TestCase
{
    public function test_top(): void
    {
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
    }

    public function test_お知らせ一覧のリンクは日本語スラッグでも二重エンコードされない(): void
    {
        // one-liner.blade.php は user リレーションを積まずに user_nickname 列を使う経路
        // （N+1回避のため）を通るので、articleSlug の urldecode() 処理を実URLで検証する。
        $article = $this->createAnnounce();
        $article->update([
            'title' => '日本語のお知らせ記事',
            'slug' => '日本語のお知らせ記事',
        ]);

        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
        $html = (string) $testResponse->getContent();
        $this->assertStringNotContainsString('%25', $html);
        $this->assertStringContainsString(e($article->fresh()?->showUrl()), $html);
    }
}
