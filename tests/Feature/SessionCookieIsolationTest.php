<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Auth\HostSessionGuard;
use App\Models\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Cookie;

class SessionCookieIsolationTest extends TestCase
{
    #[DataProvider('transportSettings')]
    public function test_session_configuration_ignores_legacy_domain_and_name(bool $secure, string $expectedName): void
    {
        $repository = Env::getRepository();
        $original = [];
        $values = ['SESSION_DOMAIN' => 'example.test', 'SESSION_COOKIE' => 'legacy_session', 'SESSION_SECURE_COOKIE' => $secure ? 'true' : 'false'];
        foreach ($values as $name => $value) {
            $original[$name] = $repository->get($name);
            $repository->set($name, $value);
        }

        try {
            $session = require config_path('session.php');
            $this->assertNull($session['domain']);
            $this->assertSame($expectedName, $session['cookie']);
            $this->assertSame($secure, $session['secure']);
            $this->assertSame('/', $session['path']);
            $this->assertSame('legacy_session', $session['legacy_cookie']);
        } finally {
            foreach ($original as $name => $value) {
                if ($value === null) {
                    $repository->clear($name);
                } else {
                    $repository->set($name, $value);
                }
            }
        }
    }

    /** @return array<string, array{bool, string}> */
    public static function transportSettings(): array
    {
        return [
            'https' => [true, '__Host-simutrans_portal_session'],
            'local http' => [false, 'simutrans_portal_local_session'],
        ];
    }

    public function test_response_issues_host_cookies_and_expires_legacy_parent_domain_cookies(): void
    {
        config([
            'session.driver' => 'array',
            'session.cookie' => '__Host-simutrans_portal_session',
            'session.domain' => null,
            'session.secure' => true,
            'session.legacy_domain' => 'example.test',
            'session.legacy_cookie' => 'legacy_session',
        ]);

        $response = $this->get('https://portal.example.test/sanctum/csrf-cookie');
        $response->assertNoContent();
        $cookies = $response->headers->getCookies();

        foreach (['__Host-simutrans_portal_session', 'XSRF-TOKEN'] as $name) {
            $cookie = $this->findCookie($cookies, $name, null);
            $this->assertTrue($cookie->isSecure());
            $this->assertSame('/', $cookie->getPath());
            $this->assertSame('lax', $cookie->getSameSite());
            $this->assertFalse($cookie->isCleared());
        }
        $this->assertTrue($this->findCookie($cookies, '__Host-simutrans_portal_session', null)->isHttpOnly());
        $this->assertFalse($this->findCookie($cookies, 'XSRF-TOKEN', null)->isHttpOnly());

        foreach (['legacy_session', 'XSRF-TOKEN', 'remember_web_'.sha1(SessionGuard::class)] as $name) {
            $this->assertTrue($this->findCookie($cookies, $name, '.example.test')->isCleared());
        }
    }

    public function test_legacy_remember_cookie_does_not_restore_authentication(): void
    {
        $user = User::factory()->create(['remember_token' => 'legacy-token']);
        $legacyName = 'remember_web_'.sha1(SessionGuard::class);
        $legacyValue = $user->id.'|legacy-token|'.$user->password;

        $this->withCookie($legacyName, $legacyValue)
            ->get('/mypage')
            ->assertRedirect(route('login'));
        $this->assertGuest('web');
    }

    #[DataProvider('rememberTransportSettings')]
    public function test_remember_login_issues_host_only_cookie_and_restores_authentication(bool $secure, string $cookieName): void
    {
        config(['session.secure' => $secure, 'session.domain' => null]);
        $guard = Auth::guard('web');
        $this->assertInstanceOf(HostSessionGuard::class, $guard);
        $user = User::factory()->create();
        $guard->login($user, true);

        $cookie = app('cookie')->queued($cookieName);
        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertNull($cookie->getDomain());
        $this->assertSame($secure, $cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('/', $cookie->getPath());

        $this->app['session']->driver()->flush();
        Auth::forgetGuards();
        $this->withCookie($cookieName, $cookie->getValue())
            ->get('/mypage')
            ->assertSuccessful();
        $this->assertAuthenticatedAs($user, 'web');
        $this->assertTrue(Auth::guard('web')->viaRemember());
    }

    /** @return array<string, array{bool, string}> */
    public static function rememberTransportSettings(): array
    {
        return [
            'https' => [true, '__Host-simutrans_portal_remember_web'],
            'local http' => [false, 'simutrans_portal_local_remember_web'],
        ];
    }

    public function test_unrelated_legacy_domain_does_not_issue_parent_cookie(): void
    {
        config(['session.legacy_domain' => 'other.test', 'session.driver' => 'array']);

        $response = $this->get('https://portal.example.test/sanctum/csrf-cookie');
        $response->assertNoContent();
        foreach ($response->headers->getCookies() as $cookie) {
            $this->assertNull($cookie->getDomain());
        }
    }

    /** @param array<Cookie> $cookies */
    private function findCookie(array $cookies, string $name, ?string $domain): Cookie
    {
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === $name && $cookie->getDomain() === $domain) {
                return $cookie;
            }
        }

        $this->fail('Expected cookie was not issued: '.$name.' ('.$domain.')');
    }
}
