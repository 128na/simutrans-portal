<?php

namespace Tests\Feature\Controllers\Api\v3\BulkZipController;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Models\BulkZip;
use App\Models\User\Bookmark;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class PublicBookmarkTest extends TestCase
{
    protected function tearDown(): void
    {
        BulkZip::all()->map(fn ($bz) => $bz->delete());
        parent::tearDown();
    }

    public function test()
    {
        Bus::fake();
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        $url = route('api.v3.bulkZip.publicBookmark', $bookmark->uuid);

        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertDispatchedAfterResponse(JobCreateBulkZip::class);
    }

    public function test_作成済み()
    {
        Bus::fake();
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        BulkZip::factory()->create([
            'bulk_zippable_id' => $bookmark->id,
            'bulk_zippable_type' => Bookmark::class,
        ]);
        $url = route('api.v3.bulkZip.publicBookmark', $bookmark->uuid);

        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertNotDispatchedAfterResponse(JobCreateBulkZip::class);
    }

    public function test_非公開()
    {
        $bookmark = Bookmark::factory()->create(['is_public' => false]);
        $url = route('api.v3.bulkZip.publicBookmark', $bookmark->uuid);

        $response = $this->getJson($url);
        $response->assertNotFound();
    }

    public function test_存在しない()
    {
        $url = route('api.v3.bulkZip.publicBookmark', 0);

        $response = $this->getJson($url);
        $response->assertNotFound();
    }
}
