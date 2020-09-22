<?php

namespace Tests\Feature\Api\v2\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
        // シード内の記事作成と作成時間をずらす
        sleep(1);
    }

    public function testShow()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'publish']);
        $category = Category::first();
        $article->categories()->attach($category->id);

        $url = route('api.v2.articles.category', $category);

        $res = $this->getJson($url);
        $res->assertStatus(200);
        $res->assertJsonFragment(['title' => $article->title]);
    }

    public function testInvalid()
    {
        $url = route('api.v2.articles.category', 65535);

        $res = $this->getJson($url);
        $res->assertStatus(404);
    }

    public function testVisibilityDraft()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'draft']);
        $category = Category::first();
        $article->categories()->attach($category->id);

        $url = route('api.v2.articles.category', $category);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

    public function testVisibilityPrivate()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'private']);
        $category = Category::first();
        $article->categories()->attach($category->id);

        $url = route('api.v2.articles.category', $category);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

    public function testVisibilityTrash()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'trash']);
        $category = Category::first();
        $article->categories()->attach($category->id);

        $url = route('api.v2.articles.category', $category);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }
}
