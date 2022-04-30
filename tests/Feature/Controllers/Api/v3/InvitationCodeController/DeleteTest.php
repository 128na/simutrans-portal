<?php

namespace Tests\Feature\Controllers\Api\v3\InvitationCodeController;

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

        $response = $this->deleteJson(route('api.v3.invitationCode.index'));
        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'invitation_code' => null]);
        $this->assertNull($response->json('data.invitation_url'));
    }
}
