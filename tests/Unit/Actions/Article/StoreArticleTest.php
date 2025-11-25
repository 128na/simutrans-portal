<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Article;

use App\Actions\Article\StoreArticle;
use App\Actions\Article\SyncRelatedModels;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Events\Article\ArticleStored;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class StoreArticleTest extends TestCase
{
    public function test投稿(): void
    {
        $user = new User;
        $data = [
            'should_notify' => true,
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Publish->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
        ];
        $carbonImmutable = new CarbonImmutable;

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $data): void {
            $article = new Article;
            $mock->expects()->store(
                \Mockery::on(function ($arg) use ($user) {
                    return $arg['user_id'] === $user->id
                        && $arg['post_type'] === ArticlePostType::AddonIntroduction
                        && $arg['title'] === 'dummy title'
                        && $arg['slug'] === 'dummy-slug'
                        && $arg['status'] === ArticleStatus::Publish
                        && $arg['contents'] === 'dummy'
                        && is_string($arg['published_at'])
                        && is_string($arg['modified_at']);
                })
            )->once()->andReturn($article);
            $this->mock(SyncRelatedModels::class, function (MockInterface $mock) use ($article, $data): void {
                $mock->expects()->__invoke($article, $data);
            });
        });

        Queue::fake();
        Event::fake();
        $result = $this->getSUT($carbonImmutable)($user, $data);
        $this->assertNotNull($result);
        Queue::assertPushed(JobUpdateRelated::class);
        Event::assertDispatched(ArticleStored::class);
    }

    public function test予約投稿(): void
    {
        $user = new User;
        $data = [
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Reservation->value,
                'contents' => 'dummy',
                'published_at' => '2022-01-02 03:34:00',
                'articles' => [],
            ],
        ];
        $carbonImmutable = new CarbonImmutable;

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $data): void {
            $article = new Article;
            $mock->expects()->store(
                \Mockery::on(function ($arg) use ($user) {
                    return $arg['user_id'] === $user->id
                        && $arg['post_type'] === ArticlePostType::AddonIntroduction
                        && $arg['title'] === 'dummy title'
                        && $arg['slug'] === 'dummy-slug'
                        && $arg['status'] === ArticleStatus::Reservation
                        && $arg['contents'] === 'dummy'
                        && $arg['published_at'] === '2022-01-02 03:34:00'
                        && is_string($arg['modified_at']);
                })
            )->once()->andReturn($article);
            $this->mock(SyncRelatedModels::class, function (MockInterface $mock) use ($article, $data): void {
                $mock->expects()->__invoke($article, $data);
            });
        });

        Queue::fake();
        Event::fake();
        $result = $this->getSUT($carbonImmutable)($user, $data);
        $this->assertNotNull($result);
        Queue::assertPushed(JobUpdateRelated::class);
        Event::assertDispatched(ArticleStored::class);
    }

    public function testそれ以外(): void
    {
        $user = new User;
        $data = [
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Draft->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
        ];
        $carbonImmutable = new CarbonImmutable;

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $data): void {
            $article = new Article;
            $mock->expects()->store(
                \Mockery::on(function ($arg) use ($user) {
                    return $arg['user_id'] === $user->id
                        && $arg['post_type'] === ArticlePostType::AddonIntroduction
                        && $arg['title'] === 'dummy title'
                        && $arg['slug'] === 'dummy-slug'
                        && $arg['status'] === ArticleStatus::Draft
                        && $arg['contents'] === 'dummy'
                        && $arg['published_at'] === null
                        && is_string($arg['modified_at']);
                })
            )->once()->andReturn($article);
            $this->mock(SyncRelatedModels::class, function (MockInterface $mock) use ($article, $data): void {
                $mock->expects()->__invoke($article, $data);
            });
        });

        Queue::fake();
        Event::fake();
        $result = $this->getSUT($carbonImmutable)($user, $data);
        $this->assertNotNull($result);
        Queue::assertPushed(JobUpdateRelated::class);
        Event::assertDispatched(ArticleStored::class);
    }

    private function getSUT(CarbonImmutable $now): StoreArticle
    {
        return app(StoreArticle::class, ['now' => $now]);
    }
}
