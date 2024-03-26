<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Mypage\InvitationCodeController;

use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public $user2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user2 = User::factory()->create();
    }

    public function test紹介者無し(): void
    {
        $this->actingAs($this->user);
        $testResponse = $this->getJson('/api/mypage/invitation_code');
        $testResponse->assertOk();

        $data = $testResponse->json('data');
        $this->assertCount(0, $data);
    }

    public function test紹介者有り(): void
    {
        $this->user2->update(['invited_by' => $this->user->id]);

        $this->actingAs($this->user);
        $testResponse = $this->getJson('/api/mypage/invitation_code');
        $testResponse->assertOk();

        $data = $testResponse->json('data');
        $this->assertCount(1, $data);
    }
}
