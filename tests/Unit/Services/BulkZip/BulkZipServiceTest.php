<?php

declare(strict_types=1);

namespace Tests\Unit\Services\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Jobs\BulkZip\JobDeleteExpiredBulkzip;
use App\Models\Attachment;
use App\Models\BulkZip;
use App\Models\User;
use App\Repositories\BulkZipRepository;
use App\Services\BulkZip\BulkZipService;
use Illuminate\Support\Facades\Bus;
use Mockery\MockInterface;
use Tests\Unit\TestCase;
use TypeError;

final class BulkZipServiceTest extends TestCase
{
    public function test(): void
    {
        Bus::fake();
        $user = new User;
        $this->mock(BulkZipRepository::class, function (MockInterface $mock) use ($user): void {
            $mock->expects()->findByBulkZippable($user)->andReturn(null);
            $mock->expects()->storeByBulkZippable($user)->andReturn(new BulkZip);
        });
        /**
         * @var BulkZipService
         */
        $bulkZipService = app(BulkZipService::class);
        $bulkZip = $bulkZipService->findOrCreateAndDispatch($user);
        $this->assertInstanceOf(BulkZip::class, $bulkZip);

        Bus::assertDispatched(JobCreateBulkZip::class);
        Bus::assertDispatched(JobDeleteExpiredBulkzip::class);
    }

    public function test未対応のモデル(): void
    {
        $this->expectException(TypeError::class);
        $bulkZipService = app(BulkZipService::class);
        $attachment = new Attachment;
        $bulkZipService->findOrCreateAndDispatch($attachment);
    }

    public function test作成済みならディスパッチしない(): void
    {
        Bus::fake();
        $this->mock(BulkZipRepository::class, function (MockInterface $mock): void {
            $mock->shouldReceive('findByBulkZippable')->andReturn(new BulkZip);
        });
        /**
         * @var BulkZipService
         */
        $bulkZipService = app(BulkZipService::class);
        $user = new User;
        $bulkZip = $bulkZipService->findOrCreateAndDispatch($user);
        $this->assertInstanceOf(BulkZip::class, $bulkZip);

        Bus::assertNotDispatched(JobCreateBulkZip::class);
        Bus::assertDispatched(JobDeleteExpiredBulkzip::class);
    }
}
