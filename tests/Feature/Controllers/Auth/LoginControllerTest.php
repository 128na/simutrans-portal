<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use App\Notifications\Loggedin;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, ?string $error_field)
    {
        Notification::fake();
        $user = User::factory()->create();

        $url = route('api.v2.login');

        $data = array_merge([
            'email' => $this->user->email,
            'password' => 'password',
        ], $data);

        $res = $this->postJson($url, $data);

        if (is_null($error_field)) {
            $res->assertOK();
            $this->assertAuthenticated();
            Notification::assertSentTo($user, Loggedin::class);
        } else {
            $res->assertJsonValidationErrors($error_field);
            Notification::assertNothingSent();
        }
    }

    public function dataValidation()
    {
        yield 'emailがnull' => [['email' => null], 'email'];
        yield 'emailが不正' => [['email' => 'invalid-email'], 'email'];
        yield '存在しないemail' => [['email' => 'missing-user@exmaple.com'], 'email'];
        yield 'PW不一致' => [['password' => '123'], 'email'];
        yield 'PWがnull' => [['password' => null], 'password'];
    }

    public function testLogout()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $url = route('api.v2.logout');
        $this->postJson($url);
        $this->assertGuest();
    }
}
