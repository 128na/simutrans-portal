<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Fortify;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\User;
use App\Notifications\SendLoggedInEmail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

class LoginTest extends TestCase
{
    private string $url = '/auth/login';

    public function test_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        Notification::fake();
        $testResponse = $this->postJson($this->url, ['email' => $user->email, 'password' => 'password']);
        $testResponse->assertOK();
        $this->assertAuthenticated();
        Notification::assertSentTo($user, SendLoggedInEmail::class);
    }

    public function test_login機能制限(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::Login], ['value' => false]);
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $testResponse = $this->postJson($this->url, ['email' => $user->email, 'password' => 'password']);
        $testResponse->assertForbidden();
    }

    public function test_6回目のログイン失敗で429になる(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        for ($i = 0; $i < 5; $i++) {
            $testResponse = $this->postJson($this->url, ['email' => $user->email, 'password' => 'wrong-password']);
            $testResponse->assertStatus(422);
        }

        $testResponse = $this->postJson($this->url, ['email' => $user->email, 'password' => 'wrong-password']);
        $testResponse->assertStatus(429);
        $this->assertGuest();
    }
}
