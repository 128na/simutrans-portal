<?php

declare(strict_types=1);

namespace Tests\Feature\Mypage;

use App\Models\User;
use Tests\Feature\TestCase;

class TokenControllerTest extends TestCase
{
    public function test_index_requires_auth(): void
    {
        $response = $this->get(route('mypage.tokens.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_shows_token_list(): void
    {
        $user = User::factory()->create();
        $user->createToken('My Token');

        $response = $this->actingAs($user)->get(route('mypage.tokens.index'));

        $response->assertOk();
        $response->assertSee('My Token');
    }

    public function test_index_does_not_show_other_users_tokens(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $other->createToken('Other Token');

        $response = $this->actingAs($user)->get(route('mypage.tokens.index'));

        $response->assertOk();
        $response->assertDontSee('Other Token');
    }

    public function test_store_creates_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('mypage.tokens.store'), [
            'name' => 'Claude Code MCP',
        ]);

        $response->assertRedirect(route('mypage.tokens.index'));
        $response->assertSessionHas('new_token');
        $response->assertSessionHas('status', 'APIトークンを発行しました');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Claude Code MCP',
        ]);
    }

    public function test_store_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('mypage.tokens.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_new_token_is_shown_on_index_after_store(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('mypage.tokens.store'), [
            'name' => 'Test Token',
        ]);

        $response = $this->actingAs($user)->followingRedirects()->post(route('mypage.tokens.store'), [
            'name' => 'Test Token 2',
        ]);

        $response->assertOk();
        $response->assertSee('一度しか表示されません');
    }

    public function test_destroy_deletes_own_token(): void
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('To Delete');
        $token = $tokenResult->accessToken;

        $response = $this->actingAs($user)->delete(route('mypage.tokens.destroy', $token->id));

        $response->assertRedirect(route('mypage.tokens.index'));
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->id]);
    }

    public function test_destroy_cannot_delete_other_users_token(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $tokenResult = $other->createToken('Other Token');
        $token = $tokenResult->accessToken;

        $this->actingAs($user)->delete(route('mypage.tokens.destroy', $token->id));

        $this->assertDatabaseHas('personal_access_tokens', ['id' => $token->id]);
    }

    public function test_mcp_does_not_reject_valid_token(): void
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('MCP Token');

        // MCP endpoint accepts POST only; verify the token is not rejected with 401
        $response = $this->withToken($tokenResult->plainTextToken)
            ->postJson('/mcp/user', []);

        $this->assertNotEquals(401, $response->getStatusCode());
    }
}
