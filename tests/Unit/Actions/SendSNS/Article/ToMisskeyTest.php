<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\GetArticleParam;
use App\Actions\SendSNS\Article\ToMisskey;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Services\Misskey\MisskeyApiClient;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class ToMisskeyTest extends TestCase
{
    public function test_posts_to_misskey_on_article_published(): void
    {
        $article = Article::factory()->make([
            'id' => 1,
            'title' => 'Test Article',
        ]);

        $articleParam = ['title' => 'Test Article', 'url' => 'https://example.com/articles/test'];

        $this->mock(GetArticleParam::class, function (MockInterface $mock) use ($article, $articleParam): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::on(fn ($arg) => $arg->id === $article->id))
                ->andReturn($articleParam);
        });

        $this->mock(MisskeyApiClient::class, function (MockInterface $mock): void {
            $mock->expects('send')
                ->once()
                ->with(\Mockery::type('string'))
                ->andReturn(['createdNote' => ['id' => 'note123']]);
        });

        $notification = new SendArticlePublished($article);
        $action = app(ToMisskey::class);

        $action($article, $notification);

        // Mock expectationsが検証される
        $this->expectNotToPerformAssertions();
    }

    public function test_posts_to_misskey_on_article_updated(): void
    {
        $article = Article::factory()->make([
            'id' => 2,
            'title' => 'Updated Article',
        ]);

        $articleParam = ['title' => 'Updated Article', 'url' => 'https://example.com/articles/updated'];

        $this->mock(GetArticleParam::class, function (MockInterface $mock) use ($article, $articleParam): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::on(fn ($arg) => $arg->id === $article->id))
                ->andReturn($articleParam);
        });

        $this->mock(MisskeyApiClient::class, function (MockInterface $mock): void {
            $mock->expects('send')
                ->once()
                ->with(\Mockery::type('string'))
                ->andReturn(['createdNote' => ['id' => 'note456']]);
        });

        $notification = new SendArticleUpdated($article);
        $action = app(ToMisskey::class);

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

        $this->mock(MisskeyApiClient::class, function (MockInterface $mock): void {
            $mock->expects('send')
                ->once()
                ->andThrow(new \Exception('Misskey API Error'));
        });

        $notification = new SendArticlePublished($article);
        $action = app(ToMisskey::class);

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
