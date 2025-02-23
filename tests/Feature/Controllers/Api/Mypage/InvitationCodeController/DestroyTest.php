<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\InvitationCodeController;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

final class DestroyTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $code = Str::uuid()->toString();
        $this->user = User::factory()->create(['invitation_code' => $code]);
    }

    public function test(): void
    {
        $this->actingAs($this->user);

        $testResponse = $this->deleteJson('/api/mypage/invitation_code');
        $testResponse->assertOk();
        $this->assertNull(User::find($this->user->id)->invitation_code);
    }
}
