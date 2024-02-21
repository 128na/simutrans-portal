<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front;

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

    public function testShow_id()
    {
        $article = Article::factory()->create(['status' => 'publish']);
        $response = $this->get(sprintf('api/front/users/%s/%s', $article->user_id, $article->slug));

        $response->assertOk();
    }

    public function testShow_nickname()
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $article = Article::factory()->create(['status' => 'publish', 'user_id' => $user->id]);
        $response = $this->get(sprintf('api/front/users/%s/%s', $user->nickname, $article->slug));

        $response->assertOk();
    }

    public function testShow非公開()
    {
        $article = Article::factory()->create(['status' => 'private']);
        $response = $this->get(sprintf('api/front/users/%s/%s', $article->user_id, $article->slug));

        $response->assertNotFound();
    }

    public function testUser_id()
    {
        $user = User::factory()->create();
        $response = $this->get('api/front/users/'.$user->id);

        $response->assertOk();
    }

    public function testUser_nickname()
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $response = $this->get('api/front/users/'.$user->nickname);

        $response->assertOk();
    }

    public function testUser存在しない()
    {
        $response = $this->get('api/front/users/0');

        $response->assertNotFound();
    }

    public function testPages()
    {
        $response = $this->get('api/front/pages');

        $response->assertOk();
    }

    public function testAnnounces()
    {
        $response = $this->get('api/front/announces');

        $response->assertOk();
    }

    public function testRanking()
    {
        $response = $this->get('api/front/ranking');

        $response->assertOk();
    }

    public function testCategory()
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(sprintf('api/front/categories/%s/%s', $category->type, $category->slug));

        $response->assertOk();
    }

    public function testCategory存在しないtype()
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get('api/front/categories/missing/'.$category->slug);

        $response->assertNotFound();
    }

    public function testCategory存在しないslug()
    {
        $category = Category::inRandomOrder()->first();
        $response = $this->get(sprintf('api/front/categories/%s/missing', $category->type));

        $response->assertNotFound();
    }

    public function testCategoryPakAddon()
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $response = $this->get(sprintf('api/front/categories/pak/%s/%s', $pak->slug, $addon->slug));

        $response->assertOk();
    }

    public function testCategoryPakAddon存在しないPak()
    {
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $response = $this->get('api/front/categories/pak/missing/'.$addon->slug);

        $response->assertNotFound();
    }

    public function testCategoryPakAddon存在しないAddon()
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $response = $this->get(sprintf('api/front/categories/pak/%s/missing', $pak->slug));

        $response->assertNotFound();
    }

    public function testCategoryPakNoneAddon()
    {
        $category = Category::inRandomOrder()->where('type', 'pak')->first();
        $response = $this->get(sprintf('api/front/categories/pak/%s/none', $category->slug));

        $response->assertOk();
    }

    public function testCategoryPakNoneAddon存在しないPak()
    {
        $response = $this->get('api/front/categories/pak/missing/none');

        $response->assertNotFound();
    }

    public function testTag()
    {
        $tag = Tag::factory()->create();
        $response = $this->get('api/front/tags/'.$tag->id);

        $response->assertOk();
    }

    public function testTag存在しない()
    {
        $response = $this->get('api/front/tags/0');

        $response->assertNotFound();
    }

    public function testSearch()
    {
        $response = $this->get('api/front/search?word=foo');

        $response->assertOk();
    }

    public function testTags()
    {
        $response = $this->get('api/front/tags');

        $response->assertOk();
    }
}
