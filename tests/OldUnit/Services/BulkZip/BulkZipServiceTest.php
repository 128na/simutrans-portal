<?php

declare(strict_types=1);

namespace Tests\OldUnit\Services\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Jobs\BulkZip\JobDeleteExpiredBulkzip;
use App\Models\Attachment;
use App\Models\BulkZip;
use App\Models\User;
use App\Repositories\BulkZipRepository;
use App\Services\BulkZip\BulkZipService;
use Illuminate\Support\Facades\Bus;
use Mockery\MockInterface;
use Tests\UnitTestCase;
use TypeError;

class BulkZipServiceTest extends UnitTestCase
{
    public function test(): void
    {
        Bus::fake();
        $this->mock(BulkZipRepository::class, function (MockInterface $mock): void {
            $mock->shouldReceive('findByBulkZippable')->andReturn(null);
            $mock->shouldReceive('storeByBulkZippable')->andReturn(new BulkZip());
        });
        /**
         * @var BulkZipService
         */
        $service = app(BulkZipService::class);
        $user = new User();
        $res = $service->findOrCreateAndDispatch($user);
        $this->assertInstanceOf(BulkZip::class, $res);

        Bus::assertDispatched(JobCreateBulkZip::class);
        Bus::assertDispatched(JobDeleteExpiredBulkzip::class);
    }

    public function test未対応のモデル(): void
    {
        $this->expectException(TypeError::class);
        $service = app(BulkZipService::class);
        $attachment = new Attachment();
        $service->findOrCreateAndDispatch($attachment);
    }

    public function test作成済みならディスパッチしない(): void
    {
        Bus::fake();
        $this->mock(BulkZipRepository::class, function (MockInterface $mock): void {
            $mock->shouldReceive('findByBulkZippable')->andReturn(new BulkZip());
        });
        /**
         * @var BulkZipService
         */
        $service = app(BulkZipService::class);
        $user = new User();
        $res = $service->findOrCreateAndDispatch($user);
        $this->assertInstanceOf(BulkZip::class, $res);

        Bus::assertNotDispatched(JobCreateBulkZip::class);
        Bus::assertDispatched(JobDeleteExpiredBulkzip::class);
    }
}
