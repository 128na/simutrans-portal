<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ArticleEditorService;

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

    public function test投稿()
    {
        $user = new User();
        $request = new StoreRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'publish',
                'contents' => 'dummy',
            ],
        ]);
        $now = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $m) use ($user, $now) {
            $m->shouldReceive('storeByUser')->withArgs([
                $user,
                [
                    'post_type' => 'addon-introduction',
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => 'publish',
                    'contents' => 'dummy',
                    'published_at' => $now->toDateTimeString(),
                    'modified_at' => $now->toDateTimeString(),
                ],
            ])->once()->andReturn(new Article());
            $m->shouldReceive('syncAttachments')->once();
            $m->shouldReceive('syncCategories')->once();
            $m->shouldReceive('syncTags')->once();
        });
        $this->getSUT($now)->storeArticle($user, $request);
    }

    public function test予約投稿()
    {
        $user = new User();
        $request = new StoreRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'reservation',
                'contents' => 'dummy',
                'published_at' => '2022-01-02 03:34:00',
            ],
        ]);
        $now = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $m) use ($user, $now) {
            $m->shouldReceive('storeByUser')->withArgs([
                $user,
                [
                    'post_type' => 'addon-introduction',
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => 'reservation',
                    'contents' => 'dummy',
                    'published_at' => '2022-01-02 03:34:00',
                    'modified_at' => $now->toDateTimeString(),
                ],
            ])->once()->andReturn(new Article());
            $m->shouldReceive('syncAttachments')->once();
            $m->shouldReceive('syncCategories')->once();
            $m->shouldReceive('syncTags')->once();
        });
        $this->getSUT($now)->storeArticle($user, $request);
    }

    public function testそれ以外()
    {
        $user = new User();
        $request = new StoreRequest([
            'article' => [
                'post_type' => 'addon-introduction',
                'title' => 'dummy title',
                'slug' => 'dummy-slug',
                'status' => 'draft',
                'contents' => 'dummy',
            ],
        ]);
        $now = new CarbonImmutable();

        $this->mock(ArticleRepository::class, function (MockInterface $m) use ($user, $now) {
            $m->shouldReceive('storeByUser')->withArgs([
                $user,
                [
                    'post_type' => 'addon-introduction',
                    'title' => 'dummy title',
                    'slug' => 'dummy-slug',
                    'status' => 'draft',
                    'contents' => 'dummy',
                    'published_at' => null,
                    'modified_at' => $now->toDateTimeString(),
                ],
            ])->once()->andReturn(new Article());
            $m->shouldReceive('syncAttachments')->once();
            $m->shouldReceive('syncCategories')->once();
            $m->shouldReceive('syncTags')->once();
        });
        $this->getSUT($now)->storeArticle($user, $request);
    }
}
