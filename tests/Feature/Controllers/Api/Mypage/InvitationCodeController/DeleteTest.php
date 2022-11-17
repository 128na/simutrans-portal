<?php

namespace Tests\Feature\Controllers\Api\Mypage\InvitationCodeController;

use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    public function test()
    {
        $oldUuid = Str::uuid()->toString();
        $this->user->update(['invitation_code' => $oldUuid]);
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => $oldUuid]);

        $this->actingAs($this->user);

        $response = $this->deleteJson('/api/mypage/invitation_code');
        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => null]);
        $this->assertNull($response->json('data.invitation_url'));
    }
}
