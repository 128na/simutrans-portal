<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserArticleCreatePageTool;
use App\Models\Category;
use App\Models\User;
use Tests\Feature\TestCase;

class UserArticleCreatePageToolTest extends TestCase
{
    public function test_creates_draft_page_article(): void
    {
        $user = User::factory()->create();
        $category = $this->pageCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreatePageTool::class, [
                'title' => 'My Page Article',
                'slug' => 'my-page-article',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'sections' => [
                    ['type' => 'text', 'text' => 'Hello world'],
                ],
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('my-page-article');
        $response->assertSee('page');
        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'slug' => 'my-page-article',
        ]);
    }

    public function test_creates_article_with_multiple_section_types(): void
    {
        $user = User::factory()->create();
        $category = $this->pageCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreatePageTool::class, [
                'title' => 'Multi Section',
                'slug' => 'multi-section',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'sections' => [
                    ['type' => 'caption', 'caption' => 'A heading'],
                    ['type' => 'text', 'text' => 'Some text'],
                    ['type' => 'url', 'url' => 'https://example.com'],
                ],
            ]);

        $response->assertOk()->assertHasNoErrors();
    }

    public function test_rejects_non_page_category(): void
    {
        $user = User::factory()->create();
        $addonCategory = Category::where('type', CategoryType::Addon)->firstOrFail();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreatePageTool::class, [
                'title' => 'Test',
                'slug' => 'test-page',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$addonCategory->id],
                'sections' => [['type' => 'text', 'text' => 'Hello']],
            ]);

        $response->assertHasErrors();
        $this->assertDatabaseMissing('articles', ['slug' => 'test-page', 'user_id' => $user->id]);
    }

    public function test_rejects_empty_sections(): void
    {
        $user = User::factory()->create();
        $category = $this->pageCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreatePageTool::class, [
                'title' => 'Test',
                'slug' => 'test-empty',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'sections' => [],
            ]);

        $response->assertHasErrors();
    }

    public function test_rejects_invalid_slug(): void
    {
        $user = User::factory()->create();
        $category = $this->pageCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreatePageTool::class, [
                'title' => 'Test',
                'slug' => 'スラッグ',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'sections' => [['type' => 'text', 'text' => 'Hello']],
            ]);

        $response->assertHasErrors();
    }

    private function pageCategory(): Category
    {
        return Category::where('type', CategoryType::Page)->where('slug', 'common')->firstOrFail();
    }
}
