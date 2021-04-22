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
        $this->bulkZip = BulkZip::factory()->create(['path' => $path]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Storage::disk('public')->delete('testing/dummy.zip');
    }

    public function test()
    {
        $url = route('bulkZips.download', $this->bulkZip->uuid);
        $response = $this->get($url);

        $response->assertStatus(200);
    }
}
