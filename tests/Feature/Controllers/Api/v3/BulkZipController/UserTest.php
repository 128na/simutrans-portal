<?php

namespace Tests\Feature\Controllers\Api\v3\BulkZipController;

use App\Models\BulkZip;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class UserTest extends TestCase
{
    protected function tearDown(): void
    {
        BulkZip::all()->map(fn ($bz) => $bz->delete());
        parent::tearDown();
    }

    public function test()
    {
        Bus::fake();
        $url = route('api.v3.bulkZip.user');

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertDispatchedAfterResponse(JobCreateBulkZip::class);
    }

    public function test_作成済み()
    {
        Bus::fake();
        BulkZip::factory()->create([
            'bulk_zippable_id' => $this->user->id,
            'bulk_zippable_type' => User::class,
        ]);
        $url = route('api.v3.bulkZip.user');

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertNotDispatchedAfterResponse(JobCreateBulkZip::class);
    }

    public function test_未ログイン()
    {
        $url = route('api.v3.bulkZip.user');

        $response = $this->getJson($url);
        $response->assertUnauthorized();
    }
}
