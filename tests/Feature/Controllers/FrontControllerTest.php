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

    public function testTop()
    {
        $response = $this->get(route('index'));

        $response->assertOk();
    }

    public function testRanking()
    {
        $response = $this->get(route('ranking'));

        $response->assertOk();
    }

    public function testPages()
    {
        $response = $this->get(route('pages'));

        $response->assertOk();
    }

    public function testAnnounces()
    {
        $response = $this->get(route('announces'));

        $response->assertOk();
    }

    public function testCategoryPakNoneAddon()
    {
        $category = Category::inRandomOrder()->where('type', 'pak')->first();
        $response = $this->get(route('category.pak.noneAddon', ['size' => $category->slug]));

        $response->assertOk();
    }

    public function testCategoryPakNoneAddon存在しないPak()
    {
        $response = $this->get(route('category.pak.noneAddon', ['size' => 'missing']));

        $response->assertNotFound();
    }

    public function testCategoryPakAddon()
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $response = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => $addon->slug]));

        $response->assertOk();
    }

    public function testCategoryPakAddon存在しないPak()
    {
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $response = $this->get(route('category.pak.addon', ['size' => 'missing', 'slug' => $addon->slug]));

        $response->assertNotFound();
    }

    public function testCategoryPakAddon存在しないAddon()
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $response = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => 'missing']));

        $response->assertNotFound();
    }

    public function testCategory()
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(route('category', ['type' => $category->type, 'slug' => $category->slug]));

        $response->assertOk();
    }

    public function testCategory存在しないtype()
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(route('category', ['type' => 'missing', 'slug' => $category->slug]));

        $response->assertNotFound();
    }

    public function testCategory存在しないslug()
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(route('category', ['type' => $category->type, 'slug' => 'missing']));

        $response->assertNotFound();
    }

    public function testTag()
    {
        $tag = Tag::factory()->create();
        $response = $this->get(route('tag', ['tag' => $tag->id]));

        $response->assertOk();
    }

    public function testTag存在しない()
    {
        $response = $this->get(route('tag', ['tag' => -1]));

        $response->assertNotFound();
    }

    public function testUser()
    {
        $user = User::factory()->create();
        $response = $this->get(route('user', ['user' => $user->id]));

        $response->assertOk();
    }

    public function testUser存在しない()
    {
        $response = $this->get(route('user', ['user' => -1]));

        $response->assertNotFound();
    }

    public function testTags()
    {
        $response = $this->get(route('tags'));

        $response->assertOk();
    }

    public function testShow()
    {
        $article = Article::factory()->create(['status' => 'publish']);
        $response = $this->get(route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]));

        $response->assertOk();
    }

    public function testShow非公開()
    {
        $article = Article::factory()->create(['status' => 'private']);
        $response = $this->get(route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]));

        $response->assertNotFound();
    }

    public function testSearch()
    {
        $response = $this->get(route('search', ['word' => 'foo']));

        $response->assertOk();
    }

    public function testFallbackShow_slug()
    {
        $article = Article::factory()->create(['status' => 'publish']);

        $response = $this->get("/articles/{$article->slug}");
        $response->assertRedirect("/users/{$article->user_id}/{$article->slug}");
    }

    public function testFallbackShow_id()
    {
        $article = Article::factory()->create(['status' => 'publish']);

        $response = $this->get("/articles/{$article->id}");
        $response->assertRedirect("/users/{$article->user_id}/{$article->slug}");
    }
}
