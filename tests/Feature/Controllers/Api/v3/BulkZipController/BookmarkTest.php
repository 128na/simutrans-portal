<?php

namespace Tests\Feature\Controllers\Api\v3\BulkZipController;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Models\BulkZip;
use App\Models\User;
use App\Models\User\Bookmark;
use Bus;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    protected function tearDown(): void
    {
        BulkZip::all()->map(fn ($bz) => $bz->delete());
        parent::tearDown();
    }

    public function test()
    {
        Bus::fake();
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        $url = route('api.v3.bulkZip.bookmark', $bookmark->id);

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertDispatchedAfterResponse(JobCreateBulkZip::class);
    }

    public function test_作成済み()
    {
        Bus::fake();
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        BulkZip::factory()->create([
            'bulk_zippable_id' => $bookmark->id,
            'bulk_zippable_type' => Bookmark::class,
        ]);
        $url = route('api.v3.bulkZip.bookmark', $bookmark->id);

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertNotDispatchedAfterResponse(JobCreateBulkZip::class);
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
