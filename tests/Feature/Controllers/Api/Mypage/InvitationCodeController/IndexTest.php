<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\InvitationCodeController;

use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public $user2;

    public function setUp(): void
    {
        parent::setUp();
        $this->user2 = User::factory()->create();
    }

    public function test紹介者無し()
    {
        $this->actingAs($this->user);
        $response = $this->getJson('/api/mypage/invitation_code');
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(0, $data);
    }

    public function test紹介者有り()
    {
        $this->user2->update(['invited_by' => $this->user->id]);

        $this->actingAs($this->user);
        $response = $this->getJson('/api/mypage/invitation_code');
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }
}
