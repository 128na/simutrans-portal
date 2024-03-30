<?php

declare(strict_types=1);

namespace Tests\Feature\Fortify;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\User;
use App\Notifications\SendLoggedInEmail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

class LoginTest extends TestCase
{
    private string $url = '/auth/login';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testLogin(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        Notification::fake();
        $testResponse = $this->postJson($this->url, ['email' => $user->email, 'password' => 'password']);
        $testResponse->assertOK();
        $this->assertAuthenticated();
        Notification::assertSentTo($user, SendLoggedInEmail::class);
    }

    public function testLogin機能制限(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::Login], ['value' => false]);
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $testResponse = $this->postJson($this->url, ['email' => $user->email, 'password' => 'password']);
        $testResponse->assertForbidden();
    }
}
