<?php

namespace Tests\Unit\Services\BulkZip;

use App\Models\BulkZip;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Services\BulkZip\ZippableManager;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;
use stdClass;
use Tests\UnitTestCase;

class ZippableManagerTest extends UnitTestCase
{
    private ZippableManager $zippableManager;
    private ArticleRepository | MockInterface $mockArticleRepository;
    private BulkZip | MockInterface $mockBulkZip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockArticleRepository = $this->mock(ArticleRepository::class);
        $this->mockBulkZip = $this->mock(BulkZip::class);

        $this->zippableManager = new ZippableManager($this->mockArticleRepository);
    }

    public function testUser()
    {
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulk_zippable_type'])->andReturn(User::class);
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulkZippable'])->andReturn(new User());
        $this->mockArticleRepository->shouldReceive('findAllByUser')->andReturn(new Collection());

        $res = $this->zippableManager->getItems($this->mockBulkZip);
        $this->assertCount(0, $res);
    }

    public function test未対応モデル()
    {
        $this->expectException(Exception::class);
        $this->mockBulkZip->shouldReceive('getAttribute')->withArgs(['bulk_zippable_type'])->andReturn(stdClass::class);

        $this->zippableManager->getItems($this->mockBulkZip);
    }
}
