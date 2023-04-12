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
        $request = new UpdateRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'publish',
                'contents' => 'dummy',
            ],
            'without_update_modified_at' => null,
        ]);
        $now = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $m) use ($article, $now) {
            $m->shouldReceive('update')->withArgs([
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => 'publish',
                    'contents' => 'dummy',
                    'modified_at' => $now->toDateTimeString(),
                ],
            ])->once()->andReturn(new Article());
            $m->shouldReceive('syncAttachments')->once();
            $m->shouldReceive('syncCategories')->once();
            $m->shouldReceive('syncTags')->once();
        });
        $this->getSUT($now)->updateArticle($article, $request);
    }

    public function test更新日を更新しない更新(): void
    {
        $article = new Article();
        $request = new UpdateRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'publish',
                'contents' => 'dummy',
            ],
            'without_update_modified_at' => '1',
        ]);
        $now = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $m) use ($article) {
            $m->shouldReceive('update')->withArgs([
                $article,
                [
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => 'publish',
                    'contents' => 'dummy',
                ],
            ])->once()->andReturn(new Article());
            $m->shouldReceive('syncAttachments')->once();
            $m->shouldReceive('syncCategories')->once();
            $m->shouldReceive('syncTags')->once();
        });
        $this->getSUT($now)->updateArticle($article, $request);
    }
}
