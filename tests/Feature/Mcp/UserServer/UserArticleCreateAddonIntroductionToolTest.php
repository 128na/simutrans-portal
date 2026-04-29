<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserArticleCreateAddonIntroductionTool;
use App\Models\Category;
use App\Models\User;
use Tests\Feature\TestCase;

class UserArticleCreateAddonIntroductionToolTest extends TestCase
{
    public function test_creates_addon_introduction_article(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonIntroductionTool::class, [
                'title' => 'My Addon Introduction',
                'slug' => 'my-addon-intro',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'link' => 'https://forum.simutrans.com/addon',
                'author' => 'Test Author',
                'description' => 'This is a test addon.',
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('my-addon-intro');
        $response->assertSee('addon-introduction');
        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'slug' => 'my-addon-intro',
        ]);
    }

    public function test_creates_published_article_with_optional_fields(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonIntroductionTool::class, [
                'title' => 'Published Addon',
                'slug' => 'published-addon',
                'status' => ArticleStatus::Publish->value,
                'categories' => [$category->id],
                'tags' => [],
                'link' => 'https://example.com',
                'author' => 'Author',
                'description' => 'Description',
                'thanks' => 'Thanks to everyone',
                'license' => 'MIT',
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee(ArticleStatus::Publish->value);
        $response->assertSee('published_at');
    }

    public function test_requires_link(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonIntroductionTool::class, [
                'title' => 'Test',
                'slug' => 'test-intro',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'author' => 'Author',
                'description' => 'Desc',
            ]);

        $response->assertHasErrors();
    }

    public function test_rejects_invalid_url(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonIntroductionTool::class, [
                'title' => 'Test',
                'slug' => 'test-intro-url',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'link' => 'not-a-url',
                'author' => 'Author',
                'description' => 'Desc',
            ]);

        $response->assertHasErrors();
    }

    public function test_rejects_nonexistent_category(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonIntroductionTool::class, [
                'title' => 'Test',
                'slug' => 'test-intro-cat',
                'status' => ArticleStatus::Draft->value,
                'categories' => [99999],
                'tags' => [],
                'link' => 'https://example.com',
                'author' => 'Author',
                'description' => 'Desc',
            ]);

        $response->assertHasErrors();
    }

    private function addonCategory(): Category
    {
        return Category::where('type', CategoryType::Addon)->firstOrFail();
    }
}
