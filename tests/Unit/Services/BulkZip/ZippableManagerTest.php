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
    private function getSUT(): ZippableManager
    {
        return app(ZippableManager::class);
    }

    public function testUser()
    {
        $this->mock(BulkZip::class, function (MockInterface $m) {
            $m->shouldReceive('getAttribute')->withArgs(['bulk_zippable_type'])->once()->andReturn(User::class);
            $m->shouldReceive('getAttribute')->withArgs(['bulkZippable'])->once()->andReturn(new User());
        });

        $this->mock(ArticleRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findAllByUser')->once()->andReturn(new Collection());
        });

        $res = $this->getSUT()->getItems(app(BulkZip::class));
        $this->assertCount(0, $res);
    }

    public function test未対応モデル()
    {
        $this->mock(BulkZip::class, function (MockInterface $m) {
            $m->shouldReceive('getAttribute')->withArgs(['bulk_zippable_type'])->twice()->andReturn(stdClass::class);
        });

        $this->expectException(Exception::class);

        $this->getSUT()->getItems(app(BulkZip::class));
    }
}
