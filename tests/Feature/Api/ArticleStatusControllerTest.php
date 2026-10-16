<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class ArticleStatusControllerTest extends TestCase
{
    #[DataProvider('authenticationModes')]
    public function test_external_status_update_is_unavailable(string $authentication): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();
        $original = $article->fresh()->getRawOriginal();

        if ($authentication === 'session') {
            $this->actingAs($user, 'web');
        } elseif ($authentication === 'token') {
            $this->withToken($user->createToken('Legacy API')->plainTextToken);
        }

        $this->patchJson('/api/v1/articles/'.$article->id.'/status', [
            'status' => ArticleStatus::Draft->value,
        ])->assertStatus(405);

        $this->assertSame($original, $article->fresh()->getRawOriginal());
    }

    /** @return array<string, array{string}> */
    public static function authenticationModes(): array
    {
        return ['guest' => ['guest'], 'session' => ['session'], 'token' => ['token']];
    }
}
