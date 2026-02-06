<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp;

use App\Enums\ArticlePostType;
use App\Enums\CategoryType;
use App\Mcp\Tools\GuestArticleSearchOptionsTool;
use App\Mcp\Tools\GuestArticleSearchTool;
use App\Mcp\Tools\GuestArticleShowTool;
use App\Mcp\Tools\GuestLatestArticlesTool;
use App\Mcp\Tools\GuestTagCategoryAggregateTool;
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

    private GuestTagCategoryAggregateTool $aggregateTool;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance('request', HttpRequest::create('/mcp', 'POST'));

        $this->optionsTool = app(GuestArticleSearchOptionsTool::class);
        $this->searchTool = app(GuestArticleSearchTool::class);
        $this->showTool = app(GuestArticleShowTool::class);
        $this->latestTool = app(GuestLatestArticlesTool::class);
        $this->aggregateTool = app(GuestTagCategoryAggregateTool::class);
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

        $pak = Category::factory()->create([
            'type' => CategoryType::Pak,
            'slug' => '128',
            'order' => 1,
        ]);
        $addon = Category::factory()->create([
            'type' => CategoryType::Addon,
            'slug' => 'building',
            'order' => 1,
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

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(Response $response): array
    {
        $json = (string) $response->content();

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
