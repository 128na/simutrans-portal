<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Models\User;
use Tests\Feature\TestCase;

final class UserControllerTest extends TestCase
{
    public function test_users(): void
    {
        $testResponse = $this->get(route('users.index'));

        $testResponse->assertOk();
    }

    public function test_user(): void
    {
        $user = User::factory()->create();
        $testResponse = $this->get(route('users.show', ['userIdOrNickname' => $user->id]));
        $testResponse->assertOk();

        $testResponse = $this->get(route('users.show', ['userIdOrNickname' => $user->nickname]));
        $testResponse->assertOk();
    }

    public function test_users_json_response(): void
    {
        User::factory()->count(3)->create();

        // Accept: application/jsonヘッダーでJSONレスポンス
        $response = $this->get('/users', ['Accept' => 'application/json']);
        $response->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                '*' => ['id', 'name', 'nickname'],
            ]);
    }

    public function test_user_json_response(): void
    {
        $user = User::factory()->create();

        // .json拡張子でJSONレスポンス
        $response = $this->get("/users/{$user->id}.json");
        $response->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'user' => ['id', 'name', 'nickname'],
                'articles',
            ]);

        // Accept: application/jsonヘッダーでJSONレスポンス
        $response = $this->get("/users/{$user->nickname}", ['Accept' => 'application/json']);
        $response->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'user' => ['id', 'name', 'nickname'],
                'articles',
            ]);
    }
}
