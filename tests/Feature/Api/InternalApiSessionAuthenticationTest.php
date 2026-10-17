<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class InternalApiSessionAuthenticationTest extends TestCase
{
    public function test_internal_api_authenticates_frontend_session(): void
    {
        config(['sanctum.stateful' => ['portal.test']]);
        $user = User::factory()->create();
        $list = MyList::factory()->for($user)->create();

        $this->withSession([Auth::guard('web')->getName() => $user->id])
            ->withHeader('Origin', 'https://portal.test')
            ->getJson('/api/v1/mylist')
            ->assertOk()
            ->assertJsonFragment(['title' => $list->title]);
    }

    #[DataProvider('internalEndpoints')]
    public function test_bearer_token_cannot_access_internal_api(string $method, string $uri, bool $withOrigin): void
    {
        config(['sanctum.stateful' => ['portal.test']]);
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();
        $list = MyList::factory()->for($user)->create();
        $item = MyListItem::factory()->create(['list_id' => $list->id, 'article_id' => $article->id]);
        $tag = Tag::factory()->create();
        $attachment = $this->createAttachment($user);
        $token = $user->createToken('Legacy unrestricted token');
        $models = [$user, $user->profile, $article, $list, $item, $tag, $attachment, $token->accessToken];
        $original = array_map(fn ($model): array => $model->fresh()->getRawOriginal(), $models);
        $counts = [Article::count(), Attachment::count(), MyList::count(), MyListItem::count(), Tag::count()];

        $uri = strtr($uri, [
            '{article}' => (string) $article->id,
            '{list}' => (string) $list->id,
            '{item}' => (string) $item->id,
            '{tag}' => (string) $tag->id,
            '{attachment}' => (string) $attachment->id,
        ]);

        $this->withToken($token->plainTextToken);
        if ($withOrigin) {
            $this->withHeader('Origin', 'https://portal.test');
        }

        $this->json($method, $uri, [
            'title' => 'Unauthorized change',
            'name' => 'Unauthorized change',
            'article_id' => $article->id,
            'note' => 'Unauthorized change',
        ])->assertUnauthorized();

        foreach ($models as $index => $model) {
            $this->assertSame($original[$index], $model->fresh()->getRawOriginal());
        }
        $this->assertSame($counts, [Article::count(), Attachment::count(), MyList::count(), MyListItem::count(), Tag::count()]);
    }

    /** @return array<string, array{string, string, bool}> */
    public static function internalEndpoints(): array
    {
        $routes = [
            ['GET', '/api/v1/mylist'],
            ['POST', '/api/v1/mylist'],
            ['PATCH', '/api/v1/mylist/{list}'],
            ['DELETE', '/api/v1/mylist/{list}'],
            ['GET', '/api/v1/mylist/{list}/items'],
            ['POST', '/api/v1/mylist/{list}/items'],
            ['PATCH', '/api/v1/mylist/{list}/items/reorder'],
            ['PATCH', '/api/v1/mylist/{list}/items/{item}'],
            ['DELETE', '/api/v1/mylist/{list}/items/{item}'],
            ['POST', '/api/v2/tags'],
            ['POST', '/api/v2/tags/{tag}'],
            ['POST', '/api/v2/attachments'],
            ['DELETE', '/api/v2/attachments/{attachment}'],
            ['POST', '/api/v2/articles'],
            ['POST', '/api/v2/articles/{article}'],
            ['DELETE', '/api/v2/articles/{article}'],
            ['POST', '/api/v2/profile'],
            ['POST', '/api/v2/analytics'],
        ];
        $cases = [];
        foreach ($routes as [$method, $uri]) {
            foreach ([false, true] as $withOrigin) {
                $cases[$method.' '.$uri.($withOrigin ? ' with origin' : ' without origin')] = [$method, $uri, $withOrigin];
            }
        }

        return $cases;
    }

    public function test_unverified_session_cannot_access_internal_api(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user)->getJson('/api/v1/mylist')->assertForbidden();
        $this->postJson('/api/v1/mylist', ['title' => 'Unverified list'])->assertForbidden();
        $this->assertSame(0, MyList::count());
    }

    #[DataProvider('csrfTokens')]
    public function test_stateful_internal_api_enforces_csrf(?string $token, int $status): void
    {
        $this->app->instance('env', 'production');
        config(['sanctum.stateful' => ['portal.test']]);
        $user = User::factory()->create();
        $this->withSession([Auth::guard('web')->getName() => $user->id, '_token' => 'valid-token'])
            ->withHeaders(['Origin' => 'https://portal.test', 'Sec-Fetch-Site' => 'same-site']);
        if ($token !== null) {
            $this->withHeader('X-CSRF-TOKEN', $token);
        }
        $this->postJson('https://portal.test/api/v1/mylist', ['title' => 'CSRF protected list'])->assertStatus($status);
        $this->assertSame($status === 201 ? 1 : 0, MyList::count());
    }

    /** @return array<string, array{?string, int}> */
    public static function csrfTokens(): array
    {
        return ['missing' => [null, 419], 'invalid' => ['incorrect-token', 419], 'valid' => ['valid-token', 201]];
    }
}
