<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\BulkZip;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Models\BulkZip;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobCreateBulkZipTest extends TestCase
{
    private BulkZip $bulkZip;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bulkZip = BulkZip::factory()->create();
    }

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

        $path = Storage::disk('public')->path($this->bulkZip->path);
        $this->assertFileExists($path, 'zipファイルが存在すること');
    }
}
