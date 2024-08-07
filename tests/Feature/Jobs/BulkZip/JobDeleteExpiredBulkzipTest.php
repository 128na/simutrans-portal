<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\BulkZip;

use App\Jobs\BulkZip\JobDeleteExpiredBulkzip;
use App\Models\BulkZip;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;

final class JobDeleteExpiredBulkzipTest extends TestCase
{
    private BulkZip $bulkzip1;

    private BulkZip $bulkzip2;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $path1 = 'testing/dummy1.zip';
        Storage::disk('public')->put($path1, 'dummy');
        $this->bulkzip1 = BulkZip::factory()->create(['created_at' => now()->modify('-1 days'), 'path' => $path1]);

        $path2 = 'testing/dummy2.zip';
        Storage::disk('public')->put($path2, 'dummy');
        $this->bulkzip2 = BulkZip::factory()->create(['path' => $path2]);
    }

    #[\Override]
    protected function tearDown(): void
    {
        $this->bulkzip1->delete();
        $this->bulkzip2->delete();
        parent::tearDown();
    }

    public function test(): void
    {
        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter
         */
        $disk = Storage::disk('public');

        $this->assertDatabaseHas('bulk_zips', ['id' => $this->bulkzip1->id]);
        $this->assertFileExists($disk->path('testing/dummy1.zip'), 'zipファイルが存在すること');

        $this->assertDatabaseHas('bulk_zips', ['id' => $this->bulkzip2->id]);
        $this->assertFileExists($disk->path('testing/dummy2.zip'), 'zipファイルが存在すること');

        JobDeleteExpiredBulkzip::dispatchSync();

        $this->assertDatabaseMissing('bulk_zips', ['id' => $this->bulkzip1->id]);
        $this->assertFileDoesNotExist($disk->path('testing/dummy1.zip'), 'zipファイルが削除されていること');

        $this->assertDatabaseHas('bulk_zips', ['id' => $this->bulkzip2->id]);
        $this->assertFileExists($disk->path('testing/dummy2.zip'), 'zipファイルが存在すること');
    }
}
