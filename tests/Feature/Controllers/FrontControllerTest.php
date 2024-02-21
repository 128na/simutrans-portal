<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Tests\TestCase;

class FrontControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testTop(): void
    {
        $response = $this->get(route('index'));

        $response->assertOk();
    }

    public function testRanking(): void
    {
        $response = $this->get(route('ranking'));

        $response->assertOk();
    }

    public function testPages(): void
    {
        $response = $this->get(route('pages'));

        $response->assertOk();
    }

    public function testAnnounces(): void
    {
        $response = $this->get(route('announces'));

        $response->assertOk();
    }

    public function testCategoryPakNoneAddon(): void
    {
        $category = Category::inRandomOrder()->where('type', 'pak')->first();
        $response = $this->get(route('category.pak.noneAddon', ['size' => $category->slug]));

        $response->assertOk();
    }

    public function testCategoryPakNoneAddon存在しないPak(): void
    {
        $response = $this->get(route('category.pak.noneAddon', ['size' => 'missing']));

        $response->assertNotFound();
    }

    public function testCategoryPakAddon(): void
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $response = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => $addon->slug]));

        $response->assertOk();
    }

    public function testCategoryPakAddon存在しないPak(): void
    {
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $response = $this->get(route('category.pak.addon', ['size' => 'missing', 'slug' => $addon->slug]));

        $response->assertNotFound();
    }

    public function testCategoryPakAddon存在しないAddon(): void
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $response = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => 'missing']));

        $response->assertNotFound();
    }

    public function testCategory(): void
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(route('category', ['type' => $category->type, 'slug' => $category->slug]));

        $response->assertOk();
    }

    public function testCategory存在しないtype(): void
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(route('category', ['type' => 'missing', 'slug' => $category->slug]));

        $response->assertNotFound();
    }

    public function testCategory存在しないslug(): void
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(route('category', ['type' => $category->type, 'slug' => 'missing']));

        $response->assertNotFound();
    }

    public function testTag(): void
    {
        $tag = Tag::factory()->create();
        $response = $this->get(route('tag', ['tag' => $tag->id]));

        $response->assertOk();
    }

    public function testTag存在しない(): void
    {
        $response = $this->get(route('tag', ['tag' => -1]));

        $response->assertNotFound();
    }

    public function testUser_id(): void
    {
        $user = User::factory()->create();
        $response = $this->get(route('user', ['userIdOrNickname' => $user->id]));

        $response->assertOk();
    }

    public function testUser_nickname(): void
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $response = $this->get(route('user', ['userIdOrNickname' => $user->nickname]));

        $response->assertOk();
    }

    public function testUser存在しない(): void
    {
        $response = $this->get(route('user', ['userIdOrNickname' => -1]));

        $response->assertNotFound();
    }

    public function testTags(): void
    {
        $response = $this->get(route('tags'));

        $response->assertOk();
    }

    public function testShow_id(): void
    {
        $article = Article::factory()->create(['status' => 'publish']);
        $response = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id, 'articleSlug' => $article->slug,
        ]));

        $response->assertOk();
    }

    public function testShow_nickname(): void
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $article = Article::factory()->create(['status' => 'publish', 'user_id' => $user->id]);
        $response = $this->get(route('articles.show', [
            'userIdOrNickname' => $user->nickname, 'articleSlug' => $article->slug,
        ]));

        $response->assertOk();
    }

    public function testShow非公開(): void
    {
        $article = Article::factory()->create(['status' => 'private']);
        $response = $this->get(route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]));

        $response->assertNotFound();
    }

    public function testSearch(): void
    {
        $response = $this->get(route('search', ['word' => 'foo']));

        $response->assertOk();
    }

    public function testFallbackShow_slug(): void
    {
        $article = Article::factory()->create(['status' => 'publish']);

        $response = $this->get('/articles/'.$article->slug);
        $response->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }

    public function testFallbackShow_id(): void
    {
        $article = Article::factory()->create(['status' => 'publish']);

        $response = $this->get('/articles/'.$article->id);
        $response->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }
}
