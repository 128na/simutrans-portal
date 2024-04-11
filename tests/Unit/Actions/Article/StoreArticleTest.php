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
    private function getSUT(CarbonImmutable $now): StoreArticle
    {
        return app(StoreArticle::class, ['now' => $now]);
    }

    public function test投稿(): void
    {
        $user = new User();
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
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable, $data): void {
            $article = new Article();
            $mock->expects()->storeByUser(
                $user,
                [
                    'post_type' => ArticlePostType::AddonIntroduction,
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => ArticleStatus::Publish,
                    'contents' => 'dummy',
                    'published_at' => $carbonImmutable->toDateTimeString(),
                    'modified_at' => $carbonImmutable->toDateTimeString(),
                ],
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
        $user = new User();
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
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable, $data): void {
            $article = new Article();
            $mock->expects()->storeByUser(
                $user,
                [
                    'post_type' => ArticlePostType::AddonIntroduction,
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => ArticleStatus::Reservation,
                    'contents' => 'dummy',
                    'published_at' => '2022-01-02 03:34:00',
                    'modified_at' => $carbonImmutable->toDateTimeString(),
                ],
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
        $user = new User();
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
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable, $data): void {
            $article = new Article();
            $mock->expects()->storeByUser(
                $user,
                [
                    'post_type' => ArticlePostType::AddonIntroduction,
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => ArticleStatus::Draft,
                    'contents' => 'dummy',
                    'published_at' => null,
                    'modified_at' => $carbonImmutable->toDateTimeString(),
                ],
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
}
