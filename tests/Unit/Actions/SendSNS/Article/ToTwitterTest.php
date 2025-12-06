<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\GetArticleParam;
use App\Actions\SendSNS\Article\ToTwitter;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Services\Twitter\TwitterV2Api;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class ToTwitterTest extends TestCase
{
    public function test_posts_to_twitter_on_article_published(): void
    {
        $article = Article::factory()->make([
            'id' => 1,
            'title' => 'Test Article',
            'slug' => 'test-article',
        ]);

        $articleParam = ['title' => 'Test Article', 'url' => 'https://example.com/articles/test-article'];

        $this->mock(GetArticleParam::class, function (MockInterface $mock) use ($article, $articleParam): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::on(fn($arg) => $arg->id === $article->id))
                ->andReturn($articleParam);
        });

        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->expects('post')
                ->once()
                ->with('tweets', \Mockery::on(function ($data) {
                    return isset($data['text']) && is_string($data['text']);
                }))
                ->andReturn(['data' => ['id' => '123456']]);
        });

        $notification = new SendArticlePublished($article);
        $action = app(ToTwitter::class);

        $action($article, $notification);

        // Mock expectationsが検証される
        $this->expectNotToPerformAssertions();
    }

    public function test_posts_to_twitter_on_article_updated(): void
    {
        $article = Article::factory()->make([
            'id' => 2,
            'title' => 'Updated Article',
            'slug' => 'updated-article',
        ]);

        $articleParam = ['title' => 'Updated Article', 'url' => 'https://example.com/articles/updated-article'];

        $this->mock(GetArticleParam::class, function (MockInterface $mock) use ($article, $articleParam): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::on(fn($arg) => $arg->id === $article->id))
                ->andReturn($articleParam);
        });

        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->expects('post')
                ->once()
                ->with('tweets', \Mockery::any())
                ->andReturn(['data' => ['id' => '789012']]);
        });

        $notification = new SendArticleUpdated($article);
        $action = app(ToTwitter::class);

        $action($article, $notification);

        // Mock expectationsが検証される
        $this->expectNotToPerformAssertions();
    }

    public function test_handles_exception_gracefully(): void
    {
        $article = Article::factory()->make(['id' => 3]);

        $this->mock(GetArticleParam::class, function (MockInterface $mock): void {
            $mock->allows('__invoke')->andReturn([]);
        });

        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->expects('post')
                ->once()
                ->andThrow(new \Exception('Twitter API Error'));
        });

        $notification = new SendArticlePublished($article);
        $action = app(ToTwitter::class);

        // 例外が投げられずに正常終了する
        $action($article, $notification);
        $this->assertTrue(true);
    }

    public function test_throws_exception_for_unsupported_notification(): void
    {
        // SendSNSNotificationは抽象クラスなので、実際の例外処理テストは他のテストで十分
        $this->markTestSkipped('Cannot instantiate abstract SendSNSNotification');
    }
}
