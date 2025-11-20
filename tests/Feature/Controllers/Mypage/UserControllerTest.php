<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

final class UserControllerTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['invitation_code' => Str::uuid()]);
    }

    public function test_show_invite(): void
    {
        $testResponse = $this->get(route('user.invite', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertOk();
    }

    public function test_show_invite_機能無効(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::InvitationCode], ['value' => false]);
        $testResponse = $this->get(route('user.invite', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertForbidden();
    }

    public function test_show_invite_無効なユーザー(): void
    {
        $this->user->delete();
        $testResponse = $this->get(route('user.invite', ['invitation_code' => $this->user->invitation_code]));
        $testResponse->assertNotFound();
    }

    public function test_show_invite_存在しないコード(): void
    {
        $this->user->delete();
        $testResponse = $this->get(route('user.invite', ['invitation_code' => Str::uuid()]));
        $testResponse->assertNotFound();
    }

    public function test_registration(): void
    {
        $testResponse = $this->post(route('user.registration', ['invitation_code' => $this->user->invitation_code]), [
            'name' => 'example',
            'email' => 'example@example.com',
            'password' => 'example123456',
            'agreement' => 'on',
        ]);
        $testResponse->assertOk();
    }

    public function test_registration_機能無効(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::InvitationCode], ['value' => false]);
        $testResponse = $this->post(route('user.registration', ['invitation_code' => $this->user->invitation_code]), [
            'name' => 'example',
            'email' => 'example@example.com',
            'password' => 'example123456',
            'agreement' => 'on',
        ]);
        $testResponse->assertForbidden();
    }

    public function test_registration_無効なユーザー(): void
    {
        $this->user->delete();
        $testResponse = $this->post(route('user.registration', ['invitation_code' => $this->user->invitation_code]), [
            'name' => 'example',
            'email' => 'example@example.com',
            'password' => 'example123456',
            'agreement' => 'on',
        ]);
        $testResponse->assertNotFound();
    }

    public function test_registration_存在しないコード(): void
    {
        $this->user->delete();
        $testResponse = $this->post(route('user.registration', ['invitation_code' => Str::uuid()]), [
            'name' => 'example',
            'email' => 'example@example.com',
            'password' => 'example123456',
            'agreement' => 'on',
        ]);
        $testResponse->assertNotFound();
    }

    public function test_show_login_guest(): void
    {
        $testResponse = $this->get(route('login'));

        $testResponse->assertOk();
    }

    public function test_show_login_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('login'));

        $testResponse->assertRedirect(route('mypage.index'));
    }

    public function test_show_two_factor_guest(): void
    {
        $testResponse = $this->get(route('two-factor.login'));

        $testResponse->assertOk();
    }

    public function test_show_forgot_password_guest(): void
    {
        $testResponse = $this->get(route('forgot-password'));

        $testResponse->assertOk();
    }

    public function test_show_reset_password_guest(): void
    {
        $token = 'test-reset-token';
        $testResponse = $this->get(route('reset-password', ['token' => $token]));

        $testResponse->assertOk();
    }
}
