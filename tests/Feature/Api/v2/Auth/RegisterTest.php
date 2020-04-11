<?php

namespace Tests\Feature\Api\v2\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testRegister()
    {
        $url = route('api.v2.register');

        $date = now()->format('YmdHis');
        $data = [
            'name' => 'example',
            'email' => "test_{$date}@example.com",
            'password' => 'password' . $date,
        ];
        $this->assertGuest();

        $response = $this->postJson($url, array_merge($data, ['name' => null]));
        $response->assertJsonValidationErrors(['name']);
        $response = $this->postJson($url, array_merge($data, ['name' => str_repeat('a', 256)]));
        $response->assertJsonValidationErrors(['name']);

        $response = $this->postJson($url, array_merge($data, ['email' => null]));
        $response->assertJsonValidationErrors(['email']);
        $response = $this->postJson($url, array_merge($data, ['email' => 'invalid-email']));
        $response->assertJsonValidationErrors(['email']);
        $registrated_user = factory(User::class)->create();
        $response = $this->postJson($url, array_merge($data, ['email' => $registrated_user->email]));
        $response->assertJsonValidationErrors(['email']);

        $response = $this->postJson($url, array_merge($data, ['password' => null]));
        $response->assertJsonValidationErrors(['password']);
        $response = $this->postJson($url, array_merge($data, ['password' => str_repeat('a', 256)]));
        $response->assertJsonValidationErrors(['password']);

        $response = $this->postJson($url, $data);
        $response->assertCreated();
        $this->assertAuthenticated();
    }
}
