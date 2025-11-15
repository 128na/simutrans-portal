<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\DeadLink;

use App\Actions\DeadLink\Check;
use App\Actions\DeadLink\FailedCountCache;
use App\Actions\DeadLink\GetHeaders;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\LazyCollection;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class CheckTest extends TestCase
{
    public function test_200(): void
    {
        $article = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock
                ->allows()
                ->getAttribute('contents')
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy', 'exclude_link_check' => false]));
        });
        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->cursorCheckLink()->once()->andReturn(LazyCollection::make([$article]));
        });
        $this->mock(GetHeaders::class, function (MockInterface $mock): void {
            $mock->expects()->__invoke('dummy')->once()->andReturn(['Status Code: 200 OK']);
        });
        $this->mock(FailedCountCache::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->clear($article)->once();
        });

        $fn = fn(): false => false;

        Queue::fake();
        $this->getSUT()($fn);
        Queue::assertNothingPushed();
    }

    public function test_200以外x2(): void
    {
        $article = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock
                ->allows()
                ->getAttribute('contents')
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy', 'exclude_link_check' => false]));
        });
        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->cursorCheckLink()->once()->andReturn(LazyCollection::make([$article]));
        });
        $this->mock(GetHeaders::class, function (MockInterface $mock): void {
            $mock
                ->expects()
                ->__invoke('dummy')
                ->times(2)
                ->andReturn(['Status Code: 500 Internal Server Error'])
                ->once()
                ->andReturn(['Status Code: 200 OK']);
        });
        $this->mock(FailedCountCache::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->clear($article)->once();
        });

        $fn = fn(): true => true;

        Queue::fake();
        $this->getSUT()($fn);
        Queue::assertNothingPushed();
    }

    public function test_200以外x3(): void
    {
        $article = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock
                ->allows()
                ->getAttribute('contents')
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy', 'exclude_link_check' => false]));
        });
        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->cursorCheckLink()->once()->andReturn(LazyCollection::make([$article]));
        });
        $this->mock(GetHeaders::class, function (MockInterface $mock): void {
            $mock->expects()->__invoke('dummy')->times(3)->andReturn(['Status Code: 500 Internal Server Error']);
        });

        $fn = fn(): true => true;

        Queue::fake();
        $this->getSUT()($fn);
        Queue::assertPushed(JobUpdateRelated::class);
    }

    public function test_除外指定時(): void
    {
        $article = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock
                ->allows()
                ->getAttribute('contents')
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy', 'exclude_link_check' => true]));
        });
        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->cursorCheckLink()->once()->andReturn(LazyCollection::make([$article]));
        });
        $fn = fn(): true => true;

        Queue::fake();
        $this->getSUT()($fn);
        Queue::assertNothingPushed();
    }

    public function test_除外ドメイン(): void
    {
        $this->mock(ArticleRepository::class, function (MockInterface $m): void {
            $article = $this->mock(Article::class, function (MockInterface $mock): void {
                $mock
                    ->allows()
                    ->getAttribute('contents')
                    ->andReturn(new AddonIntroductionContent(['link' => 'https://getuploader.com/dummy']));
            });
            $m->expects()->cursorCheckLink()->once()->andReturn(LazyCollection::make([$article]));
        });
        $fn = fn(): true => true;

        Queue::fake();
        $this->getSUT()($fn);
        Queue::assertNothingPushed();
    }

    private function getSUT(): Check
    {
        return app(Check::class);
    }
}
