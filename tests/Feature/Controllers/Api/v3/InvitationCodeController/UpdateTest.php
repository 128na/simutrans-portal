<?php

namespace Tests\Feature\Controllers\Api\v3\InvitationCodeController;

use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test新規生成()
    {
        $this->actingAs($this->user);

        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => null]);

        $response = $this->postJson(route('api.v3.invitationCode.index'));
        $response->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $this->user->id, 'invitation_code' => null]);
        $this->assertNotNull($response->json('data.invitation_url'));
    }

    public function test再生成()
    {
        $oldUuid = Str::uuid()->toString();
        $this->user->update(['invitation_code' => $oldUuid]);
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => $oldUuid]);

        $this->actingAs($this->user);

        $response = $this->postJson(route('api.v3.invitationCode.index'));
        $response->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $this->user->id, 'invitation_code' => $oldUuid]);
        $this->assertNotNull($response->json('data.invitation_url'));
    }
}
