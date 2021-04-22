<?php

namespace Tests\Feature\Controllers\Api\v3\BulkZipController;

use App\Jobs\BulkZip\CreateBulkZip;
use App\Models\BulkZip;
use App\Models\User;
use App\Models\User\Bookmark;
use Queue;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    public function test()
    {
        Queue::fake();
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        $url = route('api.v3.bulkZip.bookmark', $bookmark->id);

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Queue::assertPushed(CreateBulkZip::class);
    }

    public function test_作成済み()
    {
        Queue::fake();
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        BulkZip::factory()->create([
            'bulk_zippable_id' => $bookmark->id,
            'bulk_zippable_type' => Bookmark::class,
        ]);
        $url = route('api.v3.bulkZip.bookmark', $bookmark->id);

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Queue::assertNotPushed(CreateBulkZip::class);
    }

    public function test_未ログイン()
    {
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        $url = route('api.v3.bulkZip.bookmark', $bookmark->id);

        $response = $this->getJson($url);
        $response->assertUnauthorized();
    }

    public function test_他人のブックマーク()
    {
        $bookmark = Bookmark::factory()->create(['user_id' => User::factory()->create()->id]);
        $url = route('api.v3.bulkZip.bookmark', $bookmark->id);

        $response = $this->getJson($url);
        $response->assertUnauthorized();
    }
}
