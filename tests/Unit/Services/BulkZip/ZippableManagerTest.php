<?php

namespace Tests\Unit\Services\BulkZip;

use App\Models\BulkZip;
use App\Models\User;
use App\Models\User\Bookmark;
use App\Repositories\ArticleRepository;
use App\Repositories\User\BookmarkItemRepository;
use App\Services\BulkZip\ZippableManager;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;
use stdClass;
use Tests\UnitTestCase;

class ZippableManagerTest extends UnitTestCase
{
    private ZippableManager $zippableManager;
    private ArticleRepository|MockInterface $mockArticleRepository;
    private BookmarkItemRepository|MockInterface $mockBookmarkItemRepository;
    private BulkZip|MockInterface $mockBulkZip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockArticleRepository = $this->mock(ArticleRepository::class);
        $this->mockBookmarkItemRepository = $this->mock(BookmarkItemRepository::class);
        $this->mockBulkZip = $this->mock(BulkZip::class);

        $this->zippableManager = new ZippableManager(
            $this->mockArticleRepository,
            $this->mockBookmarkItemRepository
        );
    }

    public function test_User()
    {
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulk_zippable_type'])->andReturn(User::class);
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulkZippable'])->andReturn(new User);
        $this->mockArticleRepository->shouldReceive('finaAllByUser')->andReturn(new Collection());

        $res = $this->zippableManager->getItems($this->mockBulkZip);
        $this->assertCount(0, $res);
    }

    public function test_Bookmark()
    {
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulk_zippable_type'])->andReturn(Bookmark::class);
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulkZippable'])->andReturn(new Bookmark);
        $this->mockBookmarkItemRepository->shouldReceive('finaAllByBookmark')->andReturn(new Collection());

        $res = $this->zippableManager->getItems($this->mockBulkZip);
        $this->assertCount(0, $res);
    }

    public function test_未対応モデル()
    {
        $this->expectException(Exception::class);
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulk_zippable_type'])->andReturn(stdClass::class);

        $this->zippableManager->getItems($this->mockBulkZip);
    }
}
