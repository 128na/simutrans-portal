<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Front;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class FeedTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('ProdSeeder');
    }

    #[DataProvider('dataFeed')]
    public function test_feed(string $url): void
    {
        $testResponse = $this->get($url);
        $testResponse->assertOk();
    }

    public function test_feedの記事linkは日本語スラッグでも二重エンコードされない(): void
    {
        $article = $this->createAddonPost();
        $article->update(['title' => '日本語フィード記事', 'slug' => '日本語フィード記事']);

        $testResponse = $this->get('/feed');

        $testResponse->assertOk();
        $testResponse->assertDontSee('%25', false);
        $testResponse->assertSee($article->showUrl(), false);
    }

    public static function dataFeed(): \Generator
    {
        yield 'アドオン一覧' => ['/feed'];
        yield 'pak128' => ['/feed/pak128'];
        yield 'pak128Japan' => ['/feed/pak128-japan'];
        yield 'pak64' => ['/feed/pak64'];
        yield '一般記事' => ['/feed/page'];
        yield 'お知らせ' => ['/feed/announce'];
    }
}
