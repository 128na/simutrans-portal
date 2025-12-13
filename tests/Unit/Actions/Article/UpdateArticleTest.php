<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Article;

use App\Actions\Article\SyncRelatedModels;
use App\Actions\Article\UpdateArticle;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Events\Article\ArticleUpdated;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class UpdateArticleTest extends TestCase
{
    public function test更新(): void
    {
        $article = new Article;
        $data = [
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Publish->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
            'without_update_modified_at' => false,
        ];
        $carbonImmutable = new CarbonImmutable;

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article, $carbonImmutable, $data): void {
            $mock->expects()->update(
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => ArticleStatus::Publish,
                    'contents' => 'dummy',
                    'modified_at' => $carbonImmutable->toDateTimeString(),
                ],
            )->once()->andReturn($article);
            $this->mock(SyncRelatedModels::class, function (MockInterface $mock) use ($article, $data): void {
                $mock->expects()->__invoke($article, $data);
            });
        });

        Queue::fake();
        Event::fake();
        $result = $this->getSUT($carbonImmutable)($article, $data);
        $this->assertNotNull($result);
        Queue::assertPushed(JobUpdateRelated::class);
        Event::assertDispatched(ArticleUpdated::class);
    }

    public function test更新日を更新しない更新(): void
    {
        $article = new Article;
        $data = [
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Publish->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
            'without_update_modified_at' => true,
        ];
        $carbonImmutable = new CarbonImmutable;

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article, $data): void {
            $mock->expects()->update(
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => ArticleStatus::Publish,
                    'contents' => 'dummy',
                ],
            )->once()->andReturn($article);
            $this->mock(SyncRelatedModels::class, function (MockInterface $mock) use ($article, $data): void {
                $mock->expects()->__invoke($article, $data);
            });
        });

        Queue::fake();
        Event::fake();
        $result = $this->getSUT($carbonImmutable)($article, $data);
        $this->assertNotNull($result);
        Queue::assertPushed(JobUpdateRelated::class);
        Event::assertDispatched(ArticleUpdated::class);
    }

    private function getSUT(CarbonImmutable $now): UpdateArticle
    {
        return app(UpdateArticle::class, ['now' => $now]);
    }
}
