<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

final class InviteControllerTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['invitation_code' => Str::uuid()]);
    }

    public function test(): void
    {
        $testResponse = $this->get(route('user.invite', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertOk();
    }

    public function test機能無効(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::InvitationCode], ['value' => false]);
        $testResponse = $this->get(route('user.invite', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertForbidden();
    }

    public function test無効なユーザー(): void
    {
        $this->user->delete();
        $testResponse = $this->get(route('user.invite', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertNotFound();
    }

    public function test存在しないコード(): void
    {
        $this->user->delete();
        $testResponse = $this->get(route('user.invite', ['invitation_code' => Str::uuid()]));
        $testResponse->assertNotFound();
    }
}
