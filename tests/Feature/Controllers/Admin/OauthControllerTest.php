<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Twitter\Exceptions\InvalidStateException;
use Illuminate\Support\Facades\Session;
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
}
