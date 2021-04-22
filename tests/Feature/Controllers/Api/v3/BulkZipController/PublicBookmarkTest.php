<?php

namespace Tests\Feature\Controllers\Api\v3\BulkZipController;

use App\Jobs\BulkZip\CreateBulkZip;
use App\Models\BulkZip;
use App\Models\User\Bookmark;
use Queue;
use Tests\TestCase;

class PublicBookmarkTest extends TestCase
{
    public function test()
    {
        Queue::fake();
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        $url = route('api.v3.bulkZip.publicBookmark', $bookmark->uuid);

        $response = $this->getJson($url);
        $response->assertOk();
        Queue::assertPushed(CreateBulkZip::class);
    }

    public function test_作成済み()
    {
        Queue::fake();
        $bookmark = Bookmark::factory()->create(['is_public' => true]);
        BulkZip::factory()->create([
            'bulk_zippable_id' => $bookmark->id,
            'bulk_zippable_type' => Bookmark::class,
        ]);
        $url = route('api.v3.bulkZip.publicBookmark', $bookmark->uuid);

        $response = $this->getJson($url);
        $response->assertOk();
        Queue::assertNotPushed(CreateBulkZip::class);
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
