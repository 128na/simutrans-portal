<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class HttpEndpointsDisabledTest extends TestCase
{
    public function test_no_mcp_routes_are_registered(): void
    {
        foreach ($this->app['router']->getRoutes() as $route) {
            $this->assertFalse(
                $route->uri() === 'mcp' || str_starts_with($route->uri(), 'mcp/'),
                'Unexpected MCP route: '.$route->uri(),
            );
        }
    }

    #[DataProvider('disabledEndpoints')]
    public function test_mcp_endpoints_are_unavailable(string $method, string $uri, string $authentication): void
    {
        if ($authentication !== 'guest') {
            $user = User::factory()->create();

            if ($authentication === 'session') {
                $this->actingAs($user);
            } else {
                $this->withToken($user->createToken('Legacy MCP')->plainTextToken);
            }
        }

        $this->json($method, $uri, [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-03-26',
                'capabilities' => [],
                'clientInfo' => ['name' => 'regression-test', 'version' => '1.0'],
            ],
        ])->assertStatus($method === 'GET' ? 404 : 405);
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function disabledEndpoints(): array
    {
        $cases = [];

        foreach (['GET', 'POST', 'DELETE'] as $method) {
            foreach (['/mcp', '/mcp/user'] as $uri) {
                foreach (['guest', 'session', 'token'] as $authentication) {
                    $cases[$method.' '.$uri.' '.$authentication] = [$method, $uri, $authentication];
                }
            }
        }

        return $cases;
    }
}
