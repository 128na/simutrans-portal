<?php

namespace Tests\Feature\Controllers\InviteController;

use App\Constants\ControllOptionKeys;
use App\Models\ControllOption;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user->update(['invitation_code' => Str::uuid()]);
    }

    public function test()
    {
        $response = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $response->assertOk();
    }

    public function test機能無効()
    {
        ControllOption::create(['key' => ControllOptionKeys::INVITATION_CODE, 'value' => false]);
        $response = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $response->assertForbidden();
    }

    public function test無効なユーザー()
    {
        $this->user->delete();
        $response = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $response->assertNotFound();
    }

    public function testメール未認証のユーザー()
    {
        $this->user->update(['email_verified_at' => null]);
        $response = $this->get(route('invite.index', ['invitation_code' => $this->user->invitation_code]));
        $response->assertNotFound();
    }

    public function test存在しないコード()
    {
        $this->user->delete();
        $response = $this->get(route('invite.index', ['invitation_code' => Str::uuid()]));
        $response->assertNotFound();
    }
}
