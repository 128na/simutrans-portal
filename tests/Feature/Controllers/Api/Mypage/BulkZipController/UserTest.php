<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\BulkZipController;

use App\Jobs\BulkZip\JobCreateBulkZip;
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
        $url = '/api/mypage/bulk-zip';

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertDispatched(JobCreateBulkZip::class);
    }

    public function test作成済み()
    {
        Bus::fake();
        BulkZip::factory()->create([
            'bulk_zippable_id' => $this->user->id,
            'bulk_zippable_type' => User::class,
        ]);
        $url = '/api/mypage/bulk-zip';

        $this->actingAs($this->user);
        $response = $this->getJson($url);
        $response->assertOk();
        Bus::assertNotDispatched(JobCreateBulkZip::class);
    }

    public function test未ログイン()
    {
        $url = '/api/mypage/bulk-zip';

        $response = $this->getJson($url);
        $response->assertUnauthorized();
    }
}
