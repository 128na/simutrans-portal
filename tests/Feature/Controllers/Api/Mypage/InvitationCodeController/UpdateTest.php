<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Mypage\InvitationCodeController;

use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test新規生成(): void
    {
        $this->actingAs($this->user);

        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => null]);

        $testResponse = $this->postJson('/api/mypage/invitation_code');
        $testResponse->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $this->user->id, 'invitation_code' => null]);
        $this->assertNotNull($testResponse->json('data.invitation_url'));
    }

    public function test再生成(): void
    {
        $oldUuid = Str::uuid()->toString();
        $this->user->update(['invitation_code' => $oldUuid]);
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => $oldUuid]);

        $this->actingAs($this->user);

        $testResponse = $this->postJson('/api/mypage/invitation_code');
        $testResponse->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $this->user->id, 'invitation_code' => $oldUuid]);
        $this->assertNotNull($testResponse->json('data.invitation_url'));
    }
}
