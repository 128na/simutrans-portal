<?php

namespace Tests\Feature\Api\v2\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testLogin()
    {
        $user = factory(User::class)->create();

        $url = route('api.v2.login');

        $response = $this->postJson($url, ['email' => null, 'password' => 'password']);
        $response->assertJsonValidationErrors(['email']);
        $response = $this->postJson($url, ['email' => $user->email . 'wrong', 'password' => 'password']);
        $response->assertJsonValidationErrors(['email']);

        $response = $this->postJson($url, ['email' => $user->email, 'password' => null]);
        $response->assertJsonValidationErrors(['password']);
        $response = $this->postJson($url, ['email' => $user->email, 'password' => 'password_wrong']);
        $response->assertJsonValidationErrors(['email']);

        $response = $this->postJson($url, ['email' => $user->email, 'password' => 'password']);
        $response->assertOK();
        $this->assertAuthenticated();
    }

    public function LogoutTest()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $this->assertAuthenticated();

        $url = route('api.v2.logout');
        $response = $this->postJson($url);
        $this->assertGuest();
    }
}
