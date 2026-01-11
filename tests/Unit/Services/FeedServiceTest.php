<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\FeedService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class FeedServiceTest extends TestCase
{
    private FeedService $service;
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = Mockery::mock(ArticleRepository::class);
        $this->service = new FeedService($this->articleRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * pakAll: 全 PAK 記事取得
     */
    public function testPakAllReturnsCollection(): void
    {
        $article1 = (new Article())->forceFill(['id' => 1, 'title' => 'PAK 1']);
        $article2 = (new Article())->forceFill(['id' => 2, 'title' => 'PAK 2']);
        $articles = collect([$article1, $article2]);

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
            ->shouldReceive('getCollection')
            ->andReturn($articles)
            ->getMock();

        $this->articleRepository
            ->shouldReceive('getLatestAllPak')
            ->once()
            ->andReturn($mockPaginator);

        $result = $this->service->pakAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('PAK 1', $result[0]->title);
    }

    /**
     * @test
     * pakAll: 空コレクション対応
     */
    public function testPakAllEmptyCollection(): void
    {
        $articles = collect([]);

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
            ->shouldReceive('getCollection')
            ->andReturn($articles)
            ->getMock();

        $this->articleRepository
            ->shouldReceive('getLatestAllPak')
            ->once()
            ->andReturn($mockPaginator);

        $result = $this->service->pakAll();

        $this->assertCount(0, $result);
    }

    /**
     * @test
     * latestPak: PAK タイプ別取得
     */
    public function testLatestPakReturnsFilteredArticles(): void
    {
        $pak = 'addon-pack';
        $article = (new Article())->forceFill(['id' => 1, 'title' => 'Latest Addon Pack']);
        $articles = collect([$article]);

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
            ->shouldReceive('getCollection')
            ->andReturn($articles)
            ->getMock();

        $this->articleRepository
            ->shouldReceive('paginateLatest')
            ->with($pak)
            ->once()
            ->andReturn($mockPaginator);

        $result = $this->service->latestPak($pak);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals('Latest Addon Pack', $result[0]->title);
    }

    /**
     * @test
     * latestPak: 複数 PAK タイプ対応
     */
    public function testLatestPakDifferentTypes(): void
    {
        $types = ['addon-pack', 'object-pack', 'face-graphic'];

        foreach ($types as $pak) {
            $article = (new Article())->forceFill(['id' => 1, 'title' => "Latest $pak"]);
            $articles = collect([$article]);

            $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
                ->shouldReceive('getCollection')
                ->andReturn($articles)
                ->getMock();

            $this->articleRepository
                ->shouldReceive('paginateLatest')
                ->with($pak)
                ->andReturn($mockPaginator);

            $result = $this->service->latestPak($pak);

            $this->assertCount(1, $result);
        }
    }

    /**
     * @test
     * page: ページ記事取得
     */
    public function testPageReturnsPageArticles(): void
    {
        $article1 = (new Article())->forceFill(['id' => 1, 'title' => 'Page 1']);
        $article2 = (new Article())->forceFill(['id' => 2, 'title' => 'Page 2']);
        $article3 = (new Article())->forceFill(['id' => 3, 'title' => 'Page 3']);
        $articles = collect([$article1, $article2, $article3]);

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
            ->shouldReceive('getCollection')
            ->andReturn($articles)
            ->getMock();

        $this->articleRepository
            ->shouldReceive('paginatePages')
            ->once()
            ->andReturn($mockPaginator);

        $result = $this->service->page();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    /**
     * @test
     * page: 空ページ対応
     */
    public function testPageEmptyWhenNoPages(): void
    {
        $articles = collect([]);

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
            ->shouldReceive('getCollection')
            ->andReturn($articles)
            ->getMock();

        $this->articleRepository
            ->shouldReceive('paginatePages')
            ->once()
            ->andReturn($mockPaginator);

        $result = $this->service->page();

        $this->assertCount(0, $result);
    }

    /**
     * @test
     * announce: 告知記事取得
     */
    public function testAnnounceReturnsAnnouncements(): void
    {
        $article1 = (new Article())->forceFill(['id' => 1, 'title' => 'Announcement 1']);
        $article2 = (new Article())->forceFill(['id' => 2, 'title' => 'Announcement 2']);
        $articles = collect([$article1, $article2]);

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
            ->shouldReceive('getCollection')
            ->andReturn($articles)
            ->getMock();

        $this->articleRepository
            ->shouldReceive('paginateAnnounces')
            ->once()
            ->andReturn($mockPaginator);

        $result = $this->service->announce();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Announcement 1', $result[0]->title);
    }

    /**
     * @test
     * announce: 告知なし対応
     */
    public function testAnnounceEmptyWhenNone(): void
    {
        $articles = collect([]);

        $mockPaginator = Mockery::mock(LengthAwarePaginator::class)
            ->shouldReceive('getCollection')
            ->andReturn($articles)
            ->getMock();

        $this->articleRepository
            ->shouldReceive('paginateAnnounces')
            ->once()
            ->andReturn($mockPaginator);

        $result = $this->service->announce();

        $this->assertCount(0, $result);
    }
}
