<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp;

use App\Mcp\Tools\UserMyArticlesTool;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Passport\Passport;
use Tests\Feature\TestCase;

class UserServerToolsTest extends TestCase
{
    private UserMyArticlesTool $myArticlesTool;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance('request', HttpRequest::create('/mcp-auth', 'POST'));

        $this->myArticlesTool = app(UserMyArticlesTool::class);
    }

    public function test_my_articles_tool_returns_user_articles(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $article = Article::factory()
            ->for($user)
            ->publish()
            ->create(['slug' => 'mine']);
        $otherArticle = Article::factory()
            ->for($otherUser)
            ->publish()
            ->create(['slug' => 'other']);

        Passport::actingAs($user, ['mcp:use'], 'mcp');

        $payload = $this->decodeResponse($this->myArticlesTool->handle(new Request));

        $this->assertArrayHasKey('articles', $payload);
        $this->assertIsArray($payload['articles']);

        $ids = array_map(static fn (array $item): int => $item['id'], $payload['articles']);
        $this->assertContains($article->id, $ids);
        $this->assertNotContains($otherArticle->id, $ids);

        $firstItem = $payload['articles'][0];
        $this->assertArrayNotHasKey('email', $firstItem);
        $this->assertArrayNotHasKey('password', $firstItem);
    }

    public function test_my_articles_tool_requires_authentication(): void
    {
        $payload = $this->decodeResponse($this->myArticlesTool->handle(new Request));

        $this->assertArrayHasKey('error', $payload);
    }

    public function test_my_articles_tool_requires_mcp_scope(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['read'], 'mcp');

        $payload = $this->decodeResponse($this->myArticlesTool->handle(new Request));

        $this->assertArrayHasKey('error', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(Response $response): array
    {
        $payload = (string) $response->content();

        try {
            return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ['error' => $payload];
        }
    }
}
