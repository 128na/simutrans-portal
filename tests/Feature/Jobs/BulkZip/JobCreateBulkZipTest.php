<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Models\BulkZip;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;

final class JobCreateBulkZipTest extends TestCase
{
    private BulkZip $bulkzip;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bulkzip = BulkZip::factory()->create();
    }

    protected function tearDown(): void
    {
        $this->bulkzip->delete();
        parent::tearDown();
    }

    public function test(): void
    {
        $this->assertDatabaseHas('bulk_zips', [
            'id' => $this->bulkzip->id,
            'path' => null,
            'generated' => false,
        ]);

        JobCreateBulkZip::dispatchSync($this->bulkzip);

        $this->bulkzip->refresh();
        $this->assertDatabaseHas('bulk_zips', [
            'id' => $this->bulkzip->id,
            'generated' => true,
        ]);

        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter
         */
        $disk = Storage::disk('public');
        $this->assertFileExists($disk->path($this->bulkzip->path), 'zipファイルが存在すること');
    }
}
