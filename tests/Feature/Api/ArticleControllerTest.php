<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class ArticleControllerTest extends TestCase
{
    #[DataProvider('authenticationModes')]
    public function test_external_article_endpoints_are_unavailable(string $authentication): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->draft()->create();

        if ($authentication === 'session') {
            $this->actingAs($user, 'web');
        } elseif ($authentication === 'token') {
            $this->withToken($user->createToken('Legacy API')->plainTextToken);
        }

        $this->getJson('/api/v1/articles')->assertNotFound();
        $this->getJson('/api/v1/articles/'.$article->id)->assertNotFound();
    }

    /** @return array<string, array{string}> */
    public static function authenticationModes(): array
    {
        return ['guest' => ['guest'], 'session' => ['session'], 'token' => ['token']];
    }
}
