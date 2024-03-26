<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\User;
use App\Notifications\SendLoggedInEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, ?string $error_field): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $url = '/auth/login';

        $data = array_merge([
            'email' => $this->user->email,
            'password' => 'password',
        ], $data);

        $res = $this->postJson($url, $data);

        if (is_null($error_field)) {
            $res->assertOK();
            $this->assertAuthenticated();
            Notification::assertSentTo($user, SendLoggedInEmail::class);
        } else {
            $res->assertJsonValidationErrors($error_field);
            Notification::assertNothingSent();
        }
    }

    public static function dataValidation(): \Generator
    {
        yield 'emailがnull' => [['email' => null], 'email'];
        yield 'emailが不正' => [['email' => 'invalid-email'], 'email'];
        yield '存在しないemail' => [['email' => 'missing-user@exmaple.com'], 'email'];
        yield 'PW不一致' => [['password' => '123'], 'email'];
        yield 'PWがnull' => [['password' => null], 'password'];
    }

    public function testLogout(): void
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $url = '/auth/logout';
        $this->postJson($url);
        $this->assertGuest();
    }

    public function testLogin機能制限(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::Login], ['value' => false]);
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $url = '/auth/login';
        $data = [
            'email' => $this->user->email,
            'password' => 'password',
        ];
        $res = $this->postJson($url, $data);
        $res->assertForbidden();
    }
}
