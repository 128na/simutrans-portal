<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\InvitationCodeController;

use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private User $inviterUser;

    private User $invitedUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inviterUser = User::factory()->create();
        $this->invitedUser = User::factory()->create();
    }

    public function test(): void
    {
        $this->invitedUser->update(['invited_by' => $this->inviterUser->id]);

        $this->actingAs($this->inviterUser);
        $testResponse = $this->getJson('/api/mypage/invitation_code');
        $testResponse->assertOk();

        $data = $testResponse->json('data');
        $this->assertCount(1, $data);
    }
}
