<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserArticleCreateAddonPostTool;
use App\Models\Category;
use App\Models\User;
use Tests\Feature\TestCase;

class UserArticleCreateAddonPostToolTest extends TestCase
{
    public function test_creates_addon_post_article(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();
        $attachment = $this->createAttachment($user);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonPostTool::class, [
                'title' => 'My Addon',
                'slug' => 'my-addon',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'file_id' => $attachment->id,
                'description' => 'This is my addon.',
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('my-addon');
        $response->assertSee('addon-post');
        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'slug' => 'my-addon',
        ]);
    }

    public function test_rejects_other_users_file(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = $this->addonCategory();
        $attachment = $this->createAttachment($other);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonPostTool::class, [
                'title' => 'Test',
                'slug' => 'test-addon',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'file_id' => $attachment->id,
                'description' => 'Desc',
            ]);

        $response->assertHasErrors();
        $this->assertDatabaseMissing('articles', ['slug' => 'test-addon', 'user_id' => $user->id]);
    }

    public function test_rejects_nonexistent_file(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonPostTool::class, [
                'title' => 'Test',
                'slug' => 'test-addon-nofile',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'file_id' => 99999,
                'description' => 'Desc',
            ]);

        $response->assertHasErrors();
    }

    public function test_creates_with_optional_fields(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();
        $attachment = $this->createAttachment($user);
        $thumbnail = $this->createImageAttachment($user);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonPostTool::class, [
                'title' => 'Full Addon',
                'slug' => 'full-addon',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'file_id' => $attachment->id,
                'description' => 'Full description',
                'author' => 'My Name',
                'thanks' => 'Thanks',
                'license' => 'MIT',
                'thumbnail_id' => $thumbnail->id,
            ]);

        $response->assertOk()->assertHasNoErrors();
    }

    public function test_rejects_invalid_slug_characters(): void
    {
        $user = User::factory()->create();
        $category = $this->addonCategory();
        $attachment = $this->createAttachment($user);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateAddonPostTool::class, [
                'title' => 'Test',
                'slug' => 'スラッグ',
                'status' => ArticleStatus::Draft->value,
                'categories' => [$category->id],
                'tags' => [],
                'file_id' => $attachment->id,
                'description' => 'Desc',
            ]);

        $response->assertHasErrors();
    }

    private function addonCategory(): Category
    {
        return Category::where('type', CategoryType::Addon)->firstOrFail();
    }
}
