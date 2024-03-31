<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ArticleEditorService;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\ArticleEditorService;
use Carbon\CarbonImmutable;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class UpdateArticleTest extends TestCase
{
    private function getSUT(CarbonImmutable $now): ArticleEditorService
    {
        return app(ArticleEditorService::class, ['now' => $now]);
    }

    public function test更新(): void
    {
        $article = new Article();
        $updateRequest = new UpdateRequest([
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Publish->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
            'without_update_modified_at' => null,
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article, $carbonImmutable): void {
            $mock->expects()->update(
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => ArticleStatus::Publish,
                    'contents' => 'dummy',
                    'modified_at' => $carbonImmutable->toDateTimeString(),
                ],
            )->once()->andReturn(new Article());
            $mock->expects()->syncAttachments($article, [])->once();
            $mock->expects()->syncCategories($article, [])->once();
            $mock->expects()->syncArticles($article, [])->once();
            $mock->expects()->syncTags($article, [])->once();
        });
        $result = $this->getSUT($carbonImmutable)->updateArticle($article, $updateRequest);
        $this->assertNotNull($result);
    }

    public function test更新日を更新しない更新(): void
    {
        $article = new Article();
        $updateRequest = new UpdateRequest([
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => ArticleStatus::Publish->value,
                'contents' => 'dummy',
                'articles' => [],
            ],
            'without_update_modified_at' => '1',
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->update(
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => ArticleStatus::Publish,
                    'contents' => 'dummy',
                ],
            )->once()->andReturn(new Article());
            $mock->expects()->syncAttachments($article, [])->once();
            $mock->expects()->syncCategories($article, [])->once();
            $mock->expects()->syncArticles($article, [])->once();
            $mock->expects()->syncTags($article, [])->once();
        });
        $result = $this->getSUT($carbonImmutable)->updateArticle($article, $updateRequest);
        $this->assertNotNull($result);
    }
}
