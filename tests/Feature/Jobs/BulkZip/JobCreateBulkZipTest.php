<?php

namespace Tests\Feature\Jobs\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Models\BulkZip;
use Storage;
use Tests\TestCase;

class JobCreateBulkZipTest extends TestCase
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

    public function test()
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

        $path = Storage::disk('public')->path($this->bulkzip->path);
        $this->assertFileExists($path, 'zipファイルが存在すること');
    }
}
