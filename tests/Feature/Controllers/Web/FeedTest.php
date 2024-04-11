<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class FeedTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('ProdSeeder');
    }

    #[DataProvider('dataFeed')]
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
