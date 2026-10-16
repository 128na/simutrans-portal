<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;

class RequestForgeryProtectionTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        config(['session.driver' => 'array']);
        $this->app['env'] = 'production';
        Route::post('/_test/request-forgery', fn () => response()->noContent())->middleware('web');
    }

    public function test_same_origin_fetch_can_submit_without_a_csrf_token(): void
    {
        $this->withHeader('Sec-Fetch-Site', 'same-origin')
            ->post('https://portal.example.test/_test/request-forgery')
            ->assertNoContent();
    }

    #[DataProvider('untrustedOrigins')]
    public function test_other_origins_require_a_csrf_token(?string $fetchSite, ?string $origin): void
    {
        if ($fetchSite !== null) {
            $this->withHeader('Sec-Fetch-Site', $fetchSite);
        }
        if ($origin !== null) {
            $this->withHeader('Origin', $origin);
        }

        $this->post('https://portal.example.test/_test/request-forgery')->assertStatus(419);
    }

    /** @return array<string, array{?string, ?string}> */
    public static function untrustedOrigins(): array
    {
        return [
            'sibling host' => ['same-site', 'https://sibling.example.test'],
            'cross site' => ['cross-site', 'https://attacker.test'],
            'missing metadata' => [null, null],
        ];
    }

    public function test_a_matching_csrf_token_allows_clients_without_fetch_metadata(): void
    {
        $this->withSession(['_token' => 'valid-csrf-token'])
            ->post('https://portal.example.test/_test/request-forgery', ['_token' => 'valid-csrf-token'])
            ->assertNoContent();
    }
}
