<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Config;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user2 = User::factory()->create(['email' => 'other@example.com']);
    }

    /**
     * @dataProvider dataValidation
     */
    public function testRegister(array $data, ?string $error_field)
    {
        Config::set('app.register_restriction', false);
        $url = route('api.v2.register');

        $this->assertGuest();

        $response = $this->postJson($url, $data);
        if (is_null($error_field)) {
            $response->assertCreated();
            $this->assertAuthenticated();
        } else {
            $response->assertJsonValidationErrors($error_field);
        }
    }

    public function dataValidation()
    {
        yield 'nameがnull' => [['name' => null], 'name'];
        yield 'nameが256文字以上' => [['name' => str_repeat('a', 256)], 'name'];

        yield 'emailがnull' => [['email' => null], 'email'];
        yield 'emailが不正' => [['email' => 'invalid-email'], 'email'];
        yield 'emailが登録済み' => [['email' => 'other@example.com'], 'email'];

        yield 'passwordがnull' => [['password' => null], 'password'];
        yield 'passwordが256文字以上' => [['password' => str_repeat('a', 256)], 'password'];

        yield '成功' => [[
            'name' => 'example',
            'email' => 'test_@example.com',
            'password' => 'password',
        ], null];
    }

    public function testRegisterRestriction()
    {
        Config::set('app.register_restriction', true);
        $url = route('api.v2.register');

        $this->assertGuest();

        $response = $this->postJson($url, [
            'name' => 'example',
            'email' => 'test_@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(400);
    }
}
