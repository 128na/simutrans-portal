<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Admin;

use App\Enums\UserRole;
use App\Models\OauthToken;
use App\Models\User;
use App\Services\Twitter\Exceptions\InvalidStateException;
use App\Services\Twitter\PKCEService;
use Illuminate\Support\Facades\Session;
use Mockery;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class OauthControllerTest extends TestCase
{
    private User $adminUser;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_index_guest(): void
    {
        $testResponse = $this->get(route('admin.index'));

        $testResponse->assertRedirect(route('login'));
    }

    public function test_index_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('admin.index'));

        $testResponse->assertUnauthorized();
    }

    public function test_index_admin(): void
    {
        $this->actingAs($this->adminUser);
        $testResponse = $this->get(route('admin.index'));

        $testResponse->assertOk();
    }

    public function test_authorize_guest(): void
    {
        $testResponse = $this->get(route('admin.oauth.twitter.authorize'));

        $testResponse->assertRedirect(route('login'));
    }

    public function test_authorize_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('admin.oauth.twitter.authorize'));

        $testResponse->assertUnauthorized();
    }

    public function test_authorize_admin(): void
    {
        $this->actingAs($this->adminUser);
        $testResponse = $this->get(route('admin.oauth.twitter.authorize'));

        $testResponse->assertStatus(302); // Redirect to Twitter OAuth URL
        $this->assertTrue(Session::has('oauth2.twitter.state'));
        $this->assertTrue(Session::has('oauth2.twitter.codeVerifier'));
    }

    public function test_callback_guest(): void
    {
        $testResponse = $this->get(route('admin.oauth.twitter.callback', ['state' => 'test']));

        $testResponse->assertRedirect(route('login'));
    }

    public function test_callback_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('admin.oauth.twitter.callback', ['state' => 'test']));

        $testResponse->assertUnauthorized();
    }

    public function test_callback_admin_missing_session_state(): void
    {
        $this->actingAs($this->adminUser);

        // Session does not have oauth2.twitter.state, so it will throw InvalidStateException
        // Laravel's exception handler converts this to a 500 error
        $testResponse = $this->get(route('admin.oauth.twitter.callback', ['state' => 'test', 'code' => 'code123']));

        $testResponse->assertStatus(500);
    }

    public function test_refresh_guest(): void
    {
        $testResponse = $this->get(route('admin.oauth.twitter.refresh'));

        $testResponse->assertRedirect(route('login'));
    }

    public function test_refresh_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('admin.oauth.twitter.refresh'));

        $testResponse->assertUnauthorized();
    }

    public function test_refresh_admin_no_token(): void
    {
        $this->actingAs($this->adminUser);
        $testResponse = $this->get(route('admin.oauth.twitter.refresh'));

        $testResponse->assertRedirect(route('admin.index'));
        $testResponse->assertSessionHas('error', 'token not found');
    }

    public function test_refresh_admin_success(): void
    {
        $token = OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'old_access_token',
            'refresh_token' => 'old_refresh_token',
            'expired_at' => now()->subHour(),
        ]);

        $this->mock(PKCEService::class, function (MockInterface $mock) use ($token): void {
            $mock->expects('refreshToken')
                ->once()
                ->with(Mockery::on(fn (OauthToken $arg): bool => $arg->application === $token->application))
                ->andReturn($token);
        });

        $this->actingAs($this->adminUser);
        $testResponse = $this->get(route('admin.oauth.twitter.refresh'));

        $testResponse->assertRedirect(route('admin.index'));
        $testResponse->assertSessionHas('success', 'access token refreshed');
    }

    public function test_revoke_guest(): void
    {
        $testResponse = $this->get(route('admin.oauth.twitter.revoke'));

        $testResponse->assertRedirect(route('login'));
    }

    public function test_revoke_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('admin.oauth.twitter.revoke'));

        $testResponse->assertUnauthorized();
    }

    public function test_revoke_admin_no_token(): void
    {
        $this->actingAs($this->adminUser);
        $testResponse = $this->get(route('admin.oauth.twitter.revoke'));

        $testResponse->assertRedirect(route('admin.index'));
        $testResponse->assertSessionHas('error', 'token not found');
    }

    public function test_revoke_admin_success(): void
    {
        $token = OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'access_token_to_revoke',
            'refresh_token' => 'refresh_token_123',
            'expired_at' => now()->addHour(),
        ]);

        $this->mock(PKCEService::class, function (MockInterface $mock) use ($token): void {
            $mock->expects('revokeToken')
                ->once()
                ->with(Mockery::on(fn (OauthToken $arg): bool => $arg->application === $token->application));
        });

        $this->actingAs($this->adminUser);
        $testResponse = $this->get(route('admin.oauth.twitter.revoke'));

        $testResponse->assertRedirect(route('admin.index'));
        $testResponse->assertSessionHas('success', 'access token revoked');
    }

    public function test_callback_admin_success(): void
    {
        $token = new OauthToken([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'access_token_abc',
            'refresh_token' => 'refresh_token_xyz',
            'expired_at' => now()->addHour(),
        ]);

        $this->mock(PKCEService::class, function (MockInterface $mock) use ($token): void {
            $mock->expects('verifyState')->once()->with('test_state', 'test_state');
            $mock->expects('generateToken')->once()->with('code123', 'test_verifier')->andReturn($token);
        });

        $this->actingAs($this->adminUser);
        $this->withSession([
            'oauth2.twitter.state' => 'test_state',
            'oauth2.twitter.codeVerifier' => 'test_verifier',
        ]);
        $testResponse = $this->get(route('admin.oauth.twitter.callback', ['state' => 'test_state', 'code' => 'code123']));

        $testResponse->assertRedirect(route('admin.index'));
        $testResponse->assertSessionHas('success', 'access token created');
    }
}
