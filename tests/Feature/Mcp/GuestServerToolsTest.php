<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp;

use App\Enums\ArticlePostType;
use App\Enums\CategoryType;
use App\Mcp\Tools\GuestArticleSearchOptionsTool;
use App\Mcp\Tools\GuestArticleSearchTool;
use App\Mcp\Tools\GuestArticleShowTool;
use App\Mcp\Tools\GuestLatestArticlesTool;
use App\Mcp\Tools\GuestSearchSuggestTool;
use App\Mcp\Tools\GuestTagCategoryAggregateTool;
use App\Mcp\Tools\GuestUserArticlesTool;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Tests\Feature\TestCase;

class GuestServerToolsTest extends TestCase
{
    private GuestArticleSearchOptionsTool $optionsTool;

    private GuestArticleSearchTool $searchTool;

    private GuestArticleShowTool $showTool;

    private GuestLatestArticlesTool $latestTool;

    private GuestSearchSuggestTool $suggestTool;

    private GuestTagCategoryAggregateTool $aggregateTool;

    private GuestUserArticlesTool $userArticlesTool;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance('request', HttpRequest::create('/mcp', 'POST'));

        $this->optionsTool = app(GuestArticleSearchOptionsTool::class);
        $this->searchTool = app(GuestArticleSearchTool::class);
        $this->showTool = app(GuestArticleShowTool::class);
        $this->latestTool = app(GuestLatestArticlesTool::class);
        $this->suggestTool = app(GuestSearchSuggestTool::class);
        $this->aggregateTool = app(GuestTagCategoryAggregateTool::class);
        $this->userArticlesTool = app(GuestUserArticlesTool::class);
    }

    public function test_options_tool_returns_expected_shape(): void
    {
        $user = User::factory()->create();
        Article::factory()->for($user)->publish()->create();
        Tag::factory()->create();

        $payload = $this->decodeResponse($this->optionsTool->handle(new Request));

        $this->assertArrayHasKey('categories', $payload);
        $this->assertArrayHasKey('tags', $payload);
        $this->assertArrayHasKey('users', $payload);
        $this->assertArrayHasKey('postTypes', $payload);
        $this->assertIsArray($payload['users']);
        $this->assertIsArray($payload['tags']);
        $this->assertIsArray($payload['categories']);
        $this->assertNotEmpty($payload['users']);
        $this->assertNotEmpty($payload['tags']);
        $this->assertNotEmpty($payload['categories']);

        $firstUser = $payload['users'][0];
        $this->assertIsArray($firstUser);
        $this->assertArrayHasKey('id', $firstUser);
        $this->assertArrayHasKey('name', $firstUser);
        $this->assertArrayHasKey('nickname', $firstUser);
        $this->assertArrayNotHasKey('email', $firstUser);
        $this->assertArrayNotHasKey('password', $firstUser);
    }

    public function test_search_tool_returns_published_articles(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user)
            ->addonPost()
            ->publish()
            ->create(['slug' => 'addon-post-1']);

        $payload = $this->decodeResponse($this->searchTool->handle(new Request([
            'userIds' => [$user->id],
            'postTypes' => [ArticlePostType::AddonPost->value],
            'limit' => 10,
        ])));

        $this->assertArrayHasKey('data', $payload);
        $this->assertIsArray($payload['data']);
        $this->assertNotEmpty($payload['data']);
        $firstItem = $payload['data'][0];
        $this->assertIsArray($firstItem);
        $this->assertSame($article->id, $firstItem['id']);
        $this->assertIsArray($firstItem['user']);
        $this->assertArrayNotHasKey('email', $firstItem['user']);
        $this->assertArrayNotHasKey('password', $firstItem['user']);
    }

    public function test_show_tool_returns_article_detail(): void
    {
        $user = User::factory()->create(['nickname' => 'tester']);
        $article = Article::factory()
            ->for($user)
            ->addonPost()
            ->publish()
            ->create(['slug' => 'test-slug']);

        $payload = $this->decodeResponse($this->showTool->handle(new Request([
            'userIdOrNickname' => 'tester',
            'articleSlug' => 'test-slug',
        ])));

        $this->assertArrayHasKey('data', $payload);
        $this->assertIsArray($payload['data']);
        $this->assertSame($article->id, $payload['data']['id']);
        $this->assertSame('test-slug', $payload['data']['slug']);
        $this->assertIsArray($payload['data']['user']);
        $this->assertArrayNotHasKey('email', $payload['data']['user']);
        $this->assertArrayNotHasKey('password', $payload['data']['user']);
    }

    public function test_latest_tool_returns_pak_articles(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user)
            ->addonPost()
            ->publish()
            ->create(['slug' => 'latest-pack']);

        $pak = Category::where('type', CategoryType::Pak)
            ->where('slug', '128')
            ->firstOrFail();
        $article->categories()->save($pak);

        $payload = $this->decodeResponse($this->latestTool->handle(new Request([
            'pak' => '128',
            'limit' => 10,
        ])));

        $this->assertArrayHasKey('data', $payload);
        $this->assertNotEmpty($payload['data']);
        $ids = array_map(static fn (array $item): int => $item['id'], $payload['data']);
        $this->assertContains($article->id, $ids);
    }

    public function test_tag_category_aggregate_tool_returns_counts(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user)
            ->addonPost()
            ->publish()
            ->create(['slug' => 'tag-category-aggregate']);

        $tag = Tag::factory()->create(['name' => 'Aggregate Tag']);
        $article->tags()->attach($tag->id);

        $pak = Category::where('type', CategoryType::Pak)
            ->where('slug', '128')
            ->firstOrFail();
        $addon = Category::firstOrCreate([
            'type' => CategoryType::Addon,
            'slug' => 'building',
        ], [
            'order' => 1,
            'need_admin' => false,
        ]);
        $article->categories()->attach([$pak->id, $addon->id]);

        $payload = $this->decodeResponse($this->aggregateTool->handle(new Request));

        $this->assertArrayHasKey('tags', $payload);
        $this->assertArrayHasKey('pak_addon_categories', $payload);
        $this->assertNotEmpty($payload['tags']);
        $this->assertNotEmpty($payload['pak_addon_categories']);

        $tagIds = array_map(static fn (array $item): int => $item['id'], $payload['tags']);
        $this->assertContains($tag->id, $tagIds);

        $pakEntry = collect($payload['pak_addon_categories'])
            ->firstWhere('pak_slug', '128');
        $this->assertNotNull($pakEntry);

        $addonEntry = collect($pakEntry['addons'])
            ->firstWhere('addon_slug', 'building');
        $this->assertNotNull($addonEntry);
        $this->assertSame(1, $addonEntry['article_count']);
    }

    public function test_user_articles_tool_returns_user_articles(): void
    {
        $user = User::factory()->create(['nickname' => 'guest-user']);
        $article = Article::factory()
            ->for($user)
            ->addonPost()
            ->publish()
            ->create(['slug' => 'user-articles']);

        $payload = $this->decodeResponse($this->userArticlesTool->handle(new Request([
            'userIdOrNickname' => 'guest-user',
            'limit' => 10,
        ])));

        $this->assertArrayHasKey('user', $payload);
        $this->assertArrayHasKey('articles', $payload);
        $this->assertSame($user->id, $payload['user']['id']);
        $this->assertArrayHasKey('data', $payload['articles']);
        $this->assertNotEmpty($payload['articles']['data']);

        $ids = array_map(static fn (array $item): int => $item['id'], $payload['articles']['data']);
        $this->assertContains($article->id, $ids);
    }

    public function test_search_suggest_tool_returns_tag_suggestions(): void
    {
        Tag::factory()->create(['name' => 'AlphaTag']);
        Tag::factory()->create(['name' => 'BetaTag']);

        $payload = $this->decodeResponse($this->suggestTool->handle(new Request([
            'type' => 'tag',
            'keyword' => 'Al',
            'limit' => 10,
        ])));

        $this->assertSame('tag', $payload['type']);
        $this->assertNotEmpty($payload['items']);
        $names = array_map(static fn (array $item): string => $item['name'], $payload['items']);
        $this->assertContains('AlphaTag', $names);
        $this->assertNotContains('BetaTag', $names);
    }

    public function test_search_suggest_tool_returns_user_suggestions(): void
    {
        $user = User::factory()->create(['nickname' => 'prefix-user', 'name' => 'Prefix User']);
        Article::factory()->for($user)->addonPost()->publish()->create();
        User::factory()->create(['nickname' => 'other-user', 'name' => 'Other User']);

        $payload = $this->decodeResponse($this->suggestTool->handle(new Request([
            'type' => 'user',
            'keyword' => 'Pre',
            'limit' => 10,
        ])));

        $this->assertSame('user', $payload['type']);
        $this->assertNotEmpty($payload['items']);
        $nicknames = array_map(static fn (array $item): string => $item['nickname'], $payload['items']);
        $this->assertContains('prefix-user', $nicknames);
        $this->assertNotContains('other-user', $nicknames);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(Response $response): array
    {
        $json = (string) $response->content();

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
