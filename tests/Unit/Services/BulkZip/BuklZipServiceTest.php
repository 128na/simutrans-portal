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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class BuklZipServiceTest extends UnitTestCase
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
        $res = $service->findOrCreate($model);
        $this->assertInstanceOf(BulkZip::class, $res);

        Bus::assertDispatchedAfterResponse(JobCreateBulkZip::class);
        Bus::assertDispatchedAfterResponse(JobDeleteExpiredBulkzip::class);
    }

    public function test_未対応のモデル()
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var BulkZipService
         */
        $service = app(BulkZipService::class);
        $model = new Attachment();
        $service->findOrCreate($model);
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
        $res = $service->findOrCreate($model);
        $this->assertInstanceOf(BulkZip::class, $res);

        Bus::assertNotDispatchedAfterResponse(JobCreateBulkZip::class);
        Bus::assertNotDispatchedAfterResponse(JobDeleteExpiredBulkzip::class);
    }
}
