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

    public function testShow_id(): void
    {
        $article = Article::factory()->create(['status' => 'publish']);
        $testResponse = $this->get(sprintf('api/front/users/%s/%s', $article->user_id, $article->slug));

        $testResponse->assertOk();
    }

    public function testShow_nickname(): void
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $article = Article::factory()->create(['status' => 'publish', 'user_id' => $user->id]);
        $testResponse = $this->get(sprintf('api/front/users/%s/%s', $user->nickname, $article->slug));

        $testResponse->assertOk();
    }

    public function testShow非公開(): void
    {
        $article = Article::factory()->create(['status' => 'private']);
        $testResponse = $this->get(sprintf('api/front/users/%s/%s', $article->user_id, $article->slug));

        $testResponse->assertNotFound();
    }

    public function testUser_id(): void
    {
        $user = User::factory()->create();
        $testResponse = $this->get('api/front/users/'.$user->id);

        $testResponse->assertOk();
    }

    public function testUser_nickname(): void
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $testResponse = $this->get('api/front/users/'.$user->nickname);

        $testResponse->assertOk();
    }

    public function testUser存在しない(): void
    {
        $testResponse = $this->get('api/front/users/0');

        $testResponse->assertNotFound();
    }

    public function testPages(): void
    {
        $testResponse = $this->get('api/front/pages');

        $testResponse->assertOk();
    }

    public function testAnnounces(): void
    {
        $testResponse = $this->get('api/front/announces');

        $testResponse->assertOk();
    }

    public function testRanking(): void
    {
        $testResponse = $this->get('api/front/ranking');

        $testResponse->assertOk();
    }

    public function testCategory(): void
    {
        $category = Category::inRandomOrder()->first();
        $testResponse = $this->get(sprintf('api/front/categories/%s/%s', $category->type->value, $category->slug));

        $testResponse->assertOk();
    }

    public function testCategory存在しないtype(): void
    {
        $category = Category::inRandomOrder()->first();
        $testResponse = $this->get('api/front/categories/missing/'.$category->slug);

        $testResponse->assertNotFound();
    }

    public function testCategory存在しないslug(): void
    {
        $category = Category::inRandomOrder()->first();
        $testResponse = $this->get(sprintf('api/front/categories/%s/missing', $category->type->value));

        $testResponse->assertNotFound();
    }

    public function testCategoryPakAddon(): void
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $testResponse = $this->get(sprintf('api/front/categories/pak/%s/%s', $pak->slug, $addon->slug));

        $testResponse->assertOk();
    }

    public function testCategoryPakAddon存在しないPak(): void
    {
        $addon = Category::inRandomOrder()->where('type', 'addon')->first();
        $testResponse = $this->get('api/front/categories/pak/missing/'.$addon->slug);

        $testResponse->assertNotFound();
    }

    public function testCategoryPakAddon存在しないAddon(): void
    {
        $pak = Category::inRandomOrder()->where('type', 'pak')->first();
        $testResponse = $this->get(sprintf('api/front/categories/pak/%s/missing', $pak->slug));

        $testResponse->assertNotFound();
    }

    public function testCategoryPakNoneAddon(): void
    {
        $category = Category::inRandomOrder()->where('type', 'pak')->first();
        $testResponse = $this->get(sprintf('api/front/categories/pak/%s/none', $category->slug));

        $testResponse->assertOk();
    }

    public function testCategoryPakNoneAddon存在しないPak(): void
    {
        $testResponse = $this->get('api/front/categories/pak/missing/none');

        $testResponse->assertNotFound();
    }

    public function testTag(): void
    {
        $tag = Tag::factory()->create();
        $testResponse = $this->get('api/front/tags/'.$tag->id);

        $testResponse->assertOk();
    }

    public function testTag存在しない(): void
    {
        $testResponse = $this->get('api/front/tags/0');

        $testResponse->assertNotFound();
    }

    public function testSearch(): void
    {
        $testResponse = $this->get('api/front/search?word=foo');

        $testResponse->assertOk();
    }

    public function testTags(): void
    {
        $testResponse = $this->get('api/front/tags');

        $testResponse->assertOk();
    }
}
