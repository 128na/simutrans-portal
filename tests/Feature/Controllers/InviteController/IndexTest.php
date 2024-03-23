<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\InviteController;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user->update(['invitation_code' => Str::uuid()]);
    }

    public function test(): void
    {
        $testResponse = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertOk();
    }

    public function test機能無効(): void
    {
        ControllOption::create(['key' => ControllOptionKey::InvitationCode, 'value' => false]);
        $testResponse = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertForbidden();
    }

    public function test無効なユーザー(): void
    {
        $this->user->delete();
        $testResponse = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertNotFound();
    }

    public function testメール未認証のユーザー(): void
    {
        $this->user->update(['email_verified_at' => null]);
        $testResponse = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertNotFound();
    }

    public function test存在しないコード(): void
    {
        $this->user->delete();
        $testResponse = $this->get(route('invite.index', ['invitation_code' => Str::uuid()]));
        $testResponse->assertNotFound();
    }
}
