<?php

namespace Tests\Feature\Controllers\Api\v3\BulkZipController;

use App\Jobs\BulkZip\CreateBulkZip;
use App\Models\BulkZip;
use App\Models\User;
use Queue;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test()
    {
        Queue::fake();
        $url = route('api.v3.bulkZip.user');

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Queue::assertPushed(CreateBulkZip::class);
    }

    public function test_作成済み()
    {
        Queue::fake();
        BulkZip::factory()->create([
            'bulk_zippable_id' => $this->user->id,
            'bulk_zippable_type' => User::class,
        ]);
        $url = route('api.v3.bulkZip.user');

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Queue::assertNotPushed(CreateBulkZip::class);
    }

    public function test_未ログイン()
    {
        $url = route('api.v3.bulkZip.user');

        $response = $this->getJson($url);
        $response->assertUnauthorized();
    }
}
