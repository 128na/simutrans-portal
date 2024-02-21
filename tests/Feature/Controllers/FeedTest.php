<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use Tests\ArticleTestCase;

class FeedTest extends ArticleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createAddonPost();
        $this->createAddonIntroduction();
        $this->createPage();
        $this->createAnnounce();
    }

    /**
     * @dataProvider dataFeed
     */
    public function testFeed(string $url): void
    {
        $testResponse = $this->get($url);
        $testResponse->assertOk();
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
