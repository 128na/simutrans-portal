<?php

namespace Tests\Feature\Controllers\Api\v3\InvitationCodeController;

use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user2 = User::factory()->create();
    }

    public function test紹介者無し()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(route('api.v3.invitationCode.index'));
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(0, $data);
    }

    public function test紹介者有り()
    {
        $this->user2->update(['invited_by' => $this->user->id]);

        $this->actingAs($this->user);
        $response = $this->getJson(route('api.v3.invitationCode.index'));
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }
}
