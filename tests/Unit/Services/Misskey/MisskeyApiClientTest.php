<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Misskey;

use App\Services\Misskey\MisskeyApiClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\Unit\TestCase;

class MisskeyApiClientTest extends TestCase
{
    public function test_send_returns_response_on_success(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://misskey.example.com/notes/create' => Http::response(['createdNote' => ['id' => 'note123']], 200),
        ]);

        $sut = new MisskeyApiClient('https://misskey.example.com', 'test-token');
        $response = $sut->send('hello world');

        $this->assertTrue($response->successful());
        $this->assertSame(['createdNote' => ['id' => 'note123']], $response->json());
    }

    public function test_send_throws_request_exception_on_client_error(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://misskey.example.com/notes/create' => Http::response(['error' => 'invalid token'], 401),
        ]);

        $sut = new MisskeyApiClient('https://misskey.example.com', 'invalid-token');

        $this->expectException(RequestException::class);

        $sut->send('hello world');
    }

    public function test_send_throws_request_exception_on_server_error(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://misskey.example.com/notes/create' => Http::response('Internal Server Error', 500),
        ]);

        $sut = new MisskeyApiClient('https://misskey.example.com', 'test-token');

        $this->expectException(RequestException::class);

        $sut->send('hello world');
    }
}
