<?php

namespace Tests\Unit\Services\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Jobs\BulkZip\JobDeleteExpiredBulkzip;
use App\Models\Attachment;
use App\Models\BulkZip;
use App\Models\User;
use App\Models\User\Bookmark;
use App\Repositories\BulkZipRepository;
use App\Services\BulkZip\BulkZipService;
use Bus;
use Mockery\MockInterface;
use Tests\UnitTestCase;
use TypeError;

class BulkZipServiceTest extends UnitTestCase
{
    public function test()
    {
        Bus::fake();
        $this->mock(BulkZipRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findByBulkZippable')->andReturn(null);
            $m->shouldReceive('storeByBulkZippable')->andReturn(new BulkZip());
        });
        /**
         * @var BulkZipService
         */
        $service = app(BulkZipService::class);
        $model = new Bookmark();
        $res = $service->findOrCreateAndDispatch($model);
        $this->assertInstanceOf(BulkZip::class, $res);

        Bus::assertDispatchedAfterResponse(JobCreateBulkZip::class);
        Bus::assertDispatchedAfterResponse(JobDeleteExpiredBulkzip::class);
    }

    public function test_未対応のモデル()
    {
        $this->expectException(TypeError::class);
        $service = app(BulkZipService::class);
        $model = new Attachment();
        $service->findOrCreateAndDispatch($model);
    }

    public function test_作成済みならディスパッチしない()
    {
        Bus::fake();
        $this->mock(BulkZipRepository::class, function (MockInterface $m) {
            $m->shouldReceive('findByBulkZippable')->andReturn(new BulkZip());
        });
        /**
         * @var BulkZipService
         */
        $service = app(BulkZipService::class);
        $model = new User();
        $res = $service->findOrCreateAndDispatch($model);
        $this->assertInstanceOf(BulkZip::class, $res);

        Bus::assertNotDispatchedAfterResponse(JobCreateBulkZip::class);
        Bus::assertNotDispatchedAfterResponse(JobDeleteExpiredBulkzip::class);
    }
}
