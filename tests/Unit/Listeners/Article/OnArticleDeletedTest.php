<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\Article\ArticleDeleted;
use App\Listeners\Article\OnArticleDeleted;
use App\Models\Article;
use Illuminate\Log\Logger;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class OnArticleDeletedTest extends TestCase
{
    public function test_監査ログに記事削除が記録される(): void
    {
        $infoLogging = ['articleId' => 1, 'articleTitle' => 'テスト記事', 'articleStatus' => 'trash', 'articleUserName' => 'テストユーザー'];

        /** @var Article&MockInterface */
        $articleMock = $this->mock(Article::class, function (MockInterface $mock) use ($infoLogging): void {
            $mock->expects()->getInfoLogging()->once()->andReturn($infoLogging);
        });

        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock) use ($infoLogging): void {
            $channelMock = $this->mock(Logger::class);
            $channelMock->expects()->info('記事削除', $infoLogging)->once();
            $mock->expects()->channel('audit')->once()->andReturn($channelMock);
        });

        $listener = new OnArticleDeleted($loggerMock);
        $event = new ArticleDeleted($articleMock);

        $result = $listener->handle($event);

        $this->assertNull($result);
    }
}
