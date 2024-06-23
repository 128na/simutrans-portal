<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\BulkZipController;

use App\Jobs\BulkZip\JobCreateBulkZip;
use App\Models\BulkZip;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\Feature\TestCase;

final class UserTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[\Override]
    protected function tearDown(): void
    {
        BulkZip::all()->map(fn ($bz) => $bz->delete());
        parent::tearDown();
    }

    public function test(): void
    {
        $url = '/api/mypage/bulk-zip';
        $this->actingAs($this->user);

        Bus::fake();
        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
        Bus::assertDispatched(JobCreateBulkZip::class);
    }

    public function test作成済み(): void
    {
        Bus::fake();
        BulkZip::factory()->create([
            'bulk_zippable_id' => $this->user->id,
            'bulk_zippable_type' => User::class,
        ]);
        $url = '/api/mypage/bulk-zip';

        $this->actingAs($this->user);
        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
        Bus::assertNotDispatched(JobCreateBulkZip::class);
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/bulk-zip';

        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }
}
