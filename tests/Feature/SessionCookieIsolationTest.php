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

    public function test_session_secure_cookie_defaults_to_true_when_environment_is_unset(): void
    {
        $repository = Env::getRepository();
        $original = $repository->get('SESSION_SECURE_COOKIE');
        $repository->clear('SESSION_SECURE_COOKIE');
        try {
            $session = require config_path('session.php');
            $this->assertTrue($session['secure']);
            $this->assertSame('__Host-simutrans_portal_session', $session['cookie']);
        } finally {
            if ($original !== null) {
                $repository->set('SESSION_SECURE_COOKIE', $original);
            }
        }
    }

    #[DataProvider('legacyDomainHosts')]
    public function test_legacy_cookie_cleanup_respects_hostname_boundary(?string $domain, string $host, bool $shouldClear): void
    {
        config([
            'session.legacy_domain' => $domain,
            'session.legacy_cookie' => 'legacy_session',
            'session.driver' => 'array',
        ]);
        $response = $this->get('https://'.$host.'/sanctum/csrf-cookie')->assertNoContent();
        $parentCookies = array_values(array_filter(
            $response->headers->getCookies(),
            fn (Cookie $cookie): bool => $cookie->getDomain() === '.example.test',
        ));
        if ($shouldClear) {
            $this->assertCount(3, $parentCookies);
            foreach ($parentCookies as $cookie) {
                $this->assertTrue($cookie->isCleared());
            }
        } else {
            $this->assertSame([], $parentCookies);
        }
    }

    /** @return array<string, array{?string, string, bool}> */
    public static function legacyDomainHosts(): array
    {
        return [
            'exact domain' => ['example.test', 'example.test', true],
            'subdomain' => ['example.test', 'portal.example.test', true],
            'nested subdomain' => ['example.test', 'nested.portal.example.test', true],
            'suffix without separator' => ['example.test', 'notexample.test', false],
            'attacker suffix' => ['example.test', 'example.test.evil.test', false],
            'leading dot' => ['.example.test', 'portal.example.test', true],
            'unset domain' => [null, 'portal.example.test', false],
            'empty domain' => ['', 'portal.example.test', false],
        ];
    }

    public function test_valid_session_id_is_accepted_only_under_the_current_cookie_name(): void
    {
        config(['session.driver' => 'database', 'session.connection' => 'testing', 'session.legacy_cookie' => 'legacy_session']);
        $user = User::factory()->create();
        $this->app['session']->forgetDrivers();
        $this->app->forgetInstance('session.store');
        $this->app->forgetInstance('auth.driver');
        Auth::forgetGuards();
        $session = $this->app['session.store'];
        $session->start();
        Auth::guard('web')->login($user);
        $session->save();
        $sessionId = $session->getId();

        foreach (['legacy_session' => false, config('session.cookie') => true] as $cookieName => $accepted) {
            $this->app['session']->forgetDrivers();
            $this->app->forgetInstance('session.store');
            $this->app->forgetInstance('auth.driver');
            Auth::forgetGuards();
            $this->defaultCookies = [$cookieName => $sessionId];
            $response = $this->get('https://portal.test/mypage');
            if ($accepted) {
                $response->assertOk();
                $this->assertAuthenticatedAs($user, 'web');
            } else {
                $response->assertRedirectToRoute('login');
                $this->assertGuest('web');
            }
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
