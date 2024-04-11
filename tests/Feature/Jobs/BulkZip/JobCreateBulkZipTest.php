<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Models\BulkZip;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;

final class JobCreateBulkZipTest extends TestCase
{
    private BulkZip $bulkZip;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->bulkZip = BulkZip::factory()->create();
    }

    #[\Override]
    protected function tearDown(): void
    {
        $this->bulkZip->delete();
        parent::tearDown();
    }

    public function test(): void
    {
        $this->assertDatabaseHas('bulk_zips', [
            'id' => $this->bulkZip->id,
            'path' => null,
            'generated' => false,
        ]);

        JobCreateBulkZip::dispatchSync($this->bulkZip);

        $this->bulkZip->refresh();
        $this->assertDatabaseHas('bulk_zips', [
            'id' => $this->bulkZip->id,
            'generated' => true,
        ]);

        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter
         */
        $disk = Storage::disk('public');
        $this->assertFileExists($disk->path($this->bulkZip->path), 'zipファイルが存在すること');
    }
}
