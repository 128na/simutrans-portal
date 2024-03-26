<?php

declare(strict_types=1);

namespace Tests\OldUnit\Services\ArticleEditorService;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Services\ArticleEditorService;
use Carbon\CarbonImmutable;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class StoreArticleTest extends UnitTestCase
{
    private function getSUT(CarbonImmutable $now): ArticleEditorService
    {
        return app(ArticleEditorService::class, ['now' => $now]);
    }

    public function test投稿(): void
    {
        $user = new User();
        $storeRequest = new StoreRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'publish',
                'contents' => 'dummy',
                'articles' => [],
            ],
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable): void {
            $mock->shouldReceive('storeByUser')->withArgs([
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
            ])->once()->andReturn(new Article());
            $mock->shouldReceive('syncAttachments')->once();
            $mock->shouldReceive('syncCategories')->once();
            $mock->shouldReceive('syncArticles')->once();
            $mock->shouldReceive('syncTags')->once();
        });
        $this->getSUT($carbonImmutable)->storeArticle($user, $storeRequest);
    }

    public function test予約投稿(): void
    {
        $user = new User();
        $storeRequest = new StoreRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'reservation',
                'contents' => 'dummy',
                'published_at' => '2022-01-02 03:34:00',
                'articles' => [],
            ],
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable): void {
            $mock->shouldReceive('storeByUser')->withArgs([
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
            ])->once()->andReturn(new Article());
            $mock->shouldReceive('syncAttachments')->once();
            $mock->shouldReceive('syncCategories')->once();
            $mock->shouldReceive('syncArticles')->once();
            $mock->shouldReceive('syncTags')->once();
        });
        $this->getSUT($carbonImmutable)->storeArticle($user, $storeRequest);
    }

    public function testそれ以外(): void
    {
        $user = new User();
        $storeRequest = new StoreRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'draft',
                'contents' => 'dummy',
                'articles' => [],
            ],
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable): void {
            $mock->shouldReceive('storeByUser')->withArgs([
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
            ])->once()->andReturn(new Article());
            $mock->shouldReceive('syncAttachments')->once();
            $mock->shouldReceive('syncCategories')->once();
            $mock->shouldReceive('syncArticles')->once();
            $mock->shouldReceive('syncTags')->once();
        });
        $this->getSUT($carbonImmutable)->storeArticle($user, $storeRequest);
    }
}
