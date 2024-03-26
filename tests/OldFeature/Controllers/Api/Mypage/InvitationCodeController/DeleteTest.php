<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Mypage\InvitationCodeController;

use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    public function test(): void
    {
        $oldUuid = Str::uuid()->toString();
        $this->user->update(['invitation_code' => $oldUuid]);
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => $oldUuid]);

        $this->actingAs($this->user);

        $testResponse = $this->deleteJson('/api/mypage/invitation_code');
        $testResponse->assertOk();
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => null]);
        $this->assertNull($testResponse->json('data.invitation_url'));
    }
}
