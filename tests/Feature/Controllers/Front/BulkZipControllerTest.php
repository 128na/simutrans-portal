<?php

namespace Tests\Feature\Controllers\Front;

use App\Models\BulkZip;
use Storage;
use Tests\TestCase;

class BulkZipControllerTest extends TestCase
{
    private BulkZip $bulkZip;

    protected function setUp(): void
    {
        parent::setUp();

        $path = 'testing/dummy.zip';
        Storage::disk('public')->put($path, 'dummy');
        $this->bulkZip = BulkZip::factory()->create(['generated' => true, 'path' => $path]);
    }

    protected function tearDown(): void
    {
        $this->bulkZip->delete();
        parent::tearDown();
    }

    public function test()
    {
        $url = route('bulkZips.download', $this->bulkZip->uuid);
        $response = $this->get($url);

        $response->assertStatus(200);
    }

    public function test_未生成は404()
    {
        $this->bulkZip->update(['generated' => false]);
        $url = route('bulkZips.download', $this->bulkZip->uuid);
        $response = $this->get($url);

        $response->assertNotFound();
    }
}
