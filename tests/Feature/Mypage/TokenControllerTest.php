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

    public function test_token_issuance_is_unavailable(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/mypage/tokens', ['name' => 'Disabled Token'])
            ->assertStatus(405);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_index_has_no_token_issuance_or_mcp_instructions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('mypage.tokens.index'))
            ->assertOk()
            ->assertSee('APIトークンの新規発行は終了しました')
            ->assertDontSee('新しいトークンを発行')
            ->assertDontSee('/mcp/user')
            ->assertDontSee('name="name"', false);
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

        $this->assertModelExists($token);
    }

    public function test_mcp_is_unavailable_with_valid_token(): void
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('MCP Token');

        $response = $this->withToken($tokenResult->plainTextToken)
            ->postJson('/mcp/user', []);

        $response->assertStatus(405);
    }

    public function test_mcp_is_unavailable_with_unverified_users_token(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $tokenResult = $user->createToken('MCP Token');

        $response = $this->withToken($tokenResult->plainTextToken)
            ->postJson('/mcp/user', []);

        $response->assertStatus(405);
    }
}
