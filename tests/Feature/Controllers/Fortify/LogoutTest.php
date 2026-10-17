<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

class LogoutTest extends TestCase
{
    private string $url = '/auth/logout';

    public function test_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticated();

        $this->postJson($this->url);
        $this->assertGuest();
    }

    public function test_login_rotates_session_and_logout_rejects_old_session_and_remember_cookies(): void
    {
        config(['session.driver' => 'database', 'session.connection' => 'testing', 'session.secure' => true]);
        $this->withCredentials();
        Notification::fake();
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $cookieName = config('session.cookie');
        $rememberName = Auth::guard('web')->getRecallerName();

        $this->startBrowserRequest([]);
        $response = $this->get('https://portal.test/sanctum/csrf-cookie')->assertNoContent();
        $anonymousId = $response->getCookie($cookieName)->getValue();
        $this->assertDatabaseHas('sessions', ['id' => $anonymousId]);

        $this->startBrowserRequest([$cookieName => $anonymousId]);
        $response = $this->postJson('https://portal.test/auth/login', [
            'email' => $user->email, 'password' => 'password', 'remember' => true,
        ])->assertOk();
        $authenticatedId = $response->getCookie($cookieName)->getValue();
        $rememberValue = $response->getCookie($rememberName)->getValue();
        $csrfToken = $this->app['session.store']->token();
        $rememberToken = $user->fresh()->getRememberToken();
        $this->assertNotSame($anonymousId, $authenticatedId);
        $this->assertDatabaseMissing('sessions', ['id' => $anonymousId]);
        $this->assertDatabaseHas('sessions', ['id' => $authenticatedId, 'user_id' => $user->id]);

        $this->startBrowserRequest([$cookieName => $authenticatedId]);
        $this->get('https://portal.test/mypage')->assertOk();
        $this->assertAuthenticatedAs($user);

        $this->startBrowserRequest([$rememberName => $rememberValue]);
        $this->get('https://portal.test/mypage')->assertOk();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Auth::guard('web')->viaRemember());

        $this->startBrowserRequest([$cookieName => $authenticatedId]);
        $response = $this->postJson('https://portal.test/auth/logout')->assertNoContent();
        $this->assertGuest();
        $this->assertNotSame($authenticatedId, $response->getCookie($cookieName)->getValue());
        $this->assertNotSame($csrfToken, $this->app['session.store']->token());
        $this->assertNotSame($rememberToken, $user->fresh()->getRememberToken());
        $this->assertDatabaseMissing('sessions', ['id' => $authenticatedId]);

        foreach ([[$cookieName => $authenticatedId], [$rememberName => $rememberValue]] as $cookies) {
            $this->startBrowserRequest($cookies);
            $this->get('https://portal.test/mypage')->assertRedirectToRoute('login');
            $this->assertGuest();
        }
        $this->assertNull(DB::table('sessions')->where('id', $authenticatedId)->value('user_id'));
    }

    /** @param array<string, string> $cookies */
    private function startBrowserRequest(array $cookies): void
    {
        $this->defaultCookies = $cookies;
        $this->app['session']->forgetDrivers();
        $this->app->forgetInstance('session.store');
        $this->app->forgetInstance('auth.driver');
        Auth::forgetGuards();
    }
}
