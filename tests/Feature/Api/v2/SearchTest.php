<?php

namespace Tests\Feature\Api\v2;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
        // シード内の記事作成と作成時間をずらす
        sleep(1);
    }

    public function testValidation()
    {
        $user = factory(User::class)->create();
        $publish_article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'publish']);

        $url = route('api.v2.search', ['word' => null]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors('word');

        $url = route('api.v2.search', ['word' => '']);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors('word');

        $url = route('api.v2.search', ['word' => str_repeat('a', 101)]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors('word');

        $url = route('api.v2.search', ['word' => ['array']]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors('word');

        $url = route('api.v2.search', ['word' => str_repeat('a', 100)]);
        $res = $this->getJson($url);
        $res->assertStatus(200);
    }

    public function testSearchResult()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'publish']);
        $category = Category::first();
        $article->categories()->attach($category->id);
        $tag = factory(Tag::class)->create();
        $article->tags()->attach($tag->id);

        $url = route('api.v2.search', ['word' => $article->title]);
        $res = $this->getJson($url);
        $res->assertStatus(200);
        $res->assertJsonFragment(['title' => $article->title]);

        $url = route('api.v2.search', ['word' => $article->title . '_hoge']);
        $res = $this->getJson($url);
        $res->assertStatus(200);
        $res->assertJsonMissing(['title' => $article->title]);
    }

    public function testVisibilityDraft()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'draft']);

        $url = route('api.v2.latest', ['word' => $article->title]);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

    public function testVisibilityPrivate()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'private']);

        $url = route('api.v2.latest', ['word' => $article->title]);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

    public function testVisibilityTrash()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'trash']);

        $url = route('api.v2.latest', ['word' => $article->title]);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

}
