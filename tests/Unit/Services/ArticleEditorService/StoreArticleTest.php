<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ArticleEditorService;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Services\ArticleEditorService;
use Carbon\CarbonImmutable;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class StoreArticleTest extends TestCase
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
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Publish->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable): void {
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
            $mock->expects()->syncAttachments($article, [])->once();
            $mock->expects()->syncCategories($article, [])->once();
            $mock->expects()->syncArticles($article, [])->once();
            $mock->expects()->syncTags($article, [])->once();
        });
        $result = $this->getSUT($carbonImmutable)->storeArticle($user, $storeRequest);
        $this->assertNotNull($result);
    }

    public function test予約投稿(): void
    {
        $user = new User();
        $storeRequest = new StoreRequest([
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Reservation->value,
                'contents' => 'dummy',
                'published_at' => '2022-01-02 03:34:00',
                'articles' => [],
            ],
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable): void {
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
            $mock->expects()->syncAttachments($article, [])->once();
            $mock->expects()->syncCategories($article, [])->once();
            $mock->expects()->syncArticles($article, [])->once();
            $mock->expects()->syncTags($article, [])->once();
        });
        $result = $this->getSUT($carbonImmutable)->storeArticle($user, $storeRequest);
        $this->assertNotNull($result);
    }

    public function testそれ以外(): void
    {
        $user = new User();
        $storeRequest = new StoreRequest([
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Draft->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $carbonImmutable): void {
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
            $mock->expects()->syncAttachments($article, [])->once();
            $mock->expects()->syncCategories($article, [])->once();
            $mock->expects()->syncArticles($article, [])->once();
            $mock->expects()->syncTags($article, [])->once();
        });
        $result = $this->getSUT($carbonImmutable)->storeArticle($user, $storeRequest);
        $this->assertNotNull($result);
    }
}
