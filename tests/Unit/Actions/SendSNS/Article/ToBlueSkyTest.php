<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\GetArticleParam;
use App\Actions\SendSNS\Article\ToBluesky;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Services\BlueSky\BlueSkyApiClient;
use App\Services\BlueSky\ResizeFailedException;
use Mockery\MockInterface;
use potibm\Bluesky\Feed\Post;
use potibm\Bluesky\Response\CreateRecordResponse;
use Tests\Unit\TestCase;

final class ToBlueSkyTest extends TestCase
{
    public function test_posts_to_bluesky_on_article_published(): void
    {
        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'title' => 'Test Article',
        ]);

        $articleParam = ['title' => 'Test Article', 'url' => 'https://example.com/articles/test'];

        $this->mock(GetArticleParam::class, function (MockInterface $mock) use ($article, $articleParam): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::on(fn ($arg) => $arg->id === $article->id))
                ->andReturn($articleParam);
        });

        // CreateRecordResponse のモックは不要（BlueSkyApiClient 内で処理）
        $postMock = $this->mock(Post::class);

        $this->mock(BlueSkyApiClient::class, function (MockInterface $mock) use ($article, $postMock): void {
            $mock->expects('addWebsiteCard')
                ->once()
                ->with(\Mockery::type(Post::class), $article)
                ->andReturn($postMock);
            $mock->expects('send')
                ->once()
                ->with($postMock)
                ->andReturn('at://did:plc:123/app.bsky.feed.post/abc'); // URI文字列を直接返す
        });

        $notification = new SendArticlePublished($article);
        $action = app(ToBluesky::class);

        $action($article, $notification);

        // Mock expectationsが検証される
        $this->expectNotToPerformAssertions();
    }

    public function test_posts_to_bluesky_on_article_updated(): void
    {
        $article = Article::factory()->make(['id' => 2, 'user_id' => 1]);

        $this->mock(GetArticleParam::class, function (MockInterface $mock): void {
            $mock->allows('__invoke')->andReturn([]);
        });

        $this->mock(BlueSkyApiClient::class, function (MockInterface $mock): void {
            $mock->allows('addWebsiteCard')->andReturn($this->mock(Post::class));
            $mock->expects('send')->once()->andReturn('at://test');
        });

        $notification = new SendArticleUpdated($article);
        $action = app(ToBlueSky::class);

        $action($article, $notification);

        // Mock expectationsが検証される
        $this->expectNotToPerformAssertions();
    }

    public function test_handles_exception_gracefully(): void
    {
        $article = Article::factory()->make(['user_id' => 1]);

        $this->mock(GetArticleParam::class, function (MockInterface $mock): void {
            $mock->allows('__invoke')->andReturn([]);
        });

        $this->mock(BlueSkyApiClient::class, function (MockInterface $mock): void {
            $mock->allows('addWebsiteCard')->andThrow(new \Exception('BlueSky API Error'));
        });

        $notification = new SendArticlePublished($article);
        $action = app(ToBluesky::class);

        // 例外が投げられずに正常終了する
        $action($article, $notification);
        $this->assertTrue(true);
    }

    public function test_handles_resize_failed_exception(): void
    {
        $article = Article::factory()->make(['user_id' => 1]);

        $this->mock(GetArticleParam::class, function (MockInterface $mock): void {
            $mock->allows('__invoke')->andReturn([]);
        });

        $postMock = $this->mock(Post::class);

        $this->mock(BlueSkyApiClient::class, function (MockInterface $mock) use ($article): void {
            $mock->expects('addWebsiteCard')
                ->once()
                ->with(\Mockery::type(Post::class), $article)
                ->andThrow(new ResizeFailedException('Image resize failed'));
            // ResizeFailedException後もsendは呼ばれる（元のPostで）
            $mock->expects('send')
                ->once()
                ->with(\Mockery::type(Post::class))
                ->andReturn((object) ['uri' => 'at://test']);
        });

        $notification = new SendArticlePublished($article);
        $action = app(ToBluesky::class);

        // 例外がキャッチされて正常終了
        $action($article, $notification);
        $this->assertTrue(true);
    }
}
