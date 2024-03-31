<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\InvitationCodeController;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

class UpdateTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test新規生成(): void
    {
        $this->actingAs($this->user);

        $testResponse = $this->postJson('/api/mypage/invitation_code');
        $testResponse->assertOk();

        $this->assertNotNull(User::find($this->user->id)->invitation_code);
    }

    public function test再生成(): void
    {
        $oldCode = Str::uuid()->toString();
        $this->user->update(['invitation_code' => $oldCode]);

        $this->actingAs($this->user);

        $testResponse = $this->postJson('/api/mypage/invitation_code');
        $testResponse->assertOk();
        $this->assertNotSame($oldCode, User::find($this->user->id)->invitation_code);
    }
}
