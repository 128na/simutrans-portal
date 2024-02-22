<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ArticleEditorService;

use App\Http\Requests\Api\Article\UpdateRequest;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\ArticleEditorService;
use Carbon\CarbonImmutable;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class UpdateArticleTest extends UnitTestCase
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
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'publish',
                'contents' => 'dummy',
            ],
            'without_update_modified_at' => null,
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article, $carbonImmutable): void {
            $mock->shouldReceive('update')->withArgs([
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => 'publish',
                    'contents' => 'dummy',
                    'modified_at' => $carbonImmutable->toDateTimeString(),
                ],
            ])->once()->andReturn(new Article());
            $mock->shouldReceive('syncAttachments')->once();
            $mock->shouldReceive('syncCategories')->once();
            $mock->shouldReceive('syncTags')->once();
        });
        $this->getSUT($carbonImmutable)->updateArticle($article, $updateRequest);
    }

    public function test更新日を更新しない更新(): void
    {
        $article = new Article();
        $updateRequest = new UpdateRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'publish',
                'contents' => 'dummy',
            ],
            'without_update_modified_at' => '1',
        ]);
        $carbonImmutable = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->shouldReceive('update')->withArgs([
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => 'publish',
                    'contents' => 'dummy',
                ],
            ])->once()->andReturn(new Article());
            $mock->shouldReceive('syncAttachments')->once();
            $mock->shouldReceive('syncCategories')->once();
            $mock->shouldReceive('syncTags')->once();
        });
        $this->getSUT($carbonImmutable)->updateArticle($article, $updateRequest);
    }
}
