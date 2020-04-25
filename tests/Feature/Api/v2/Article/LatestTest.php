<?php

namespace Tests\Feature\Api\v2\Article;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LatestTest extends TestCase
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
        $user = factory(User::class)->create();
        $publish_article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'publish']);

        $url = route('api.v2.articles.latest');

        $res = $this->getJson($url);
        $res->assertStatus(200);
    }

    public function testThrottle()
    {
        $url = route('api.v2.articles.latest');

        $throttle_limit = 100;
        for ($i = 0; $i < $throttle_limit; $i++) {
            $res = $this->getJson($url);
            $res->assertStatus(200);
        }
        $res = $this->getJson($url);
        $res->assertStatus(429);
    }

    public function testData()
    {
        $user = factory(User::class)->create();
        $article = $this->createAddonPost($user);
        $category = Category::first();
        $article->categories()->attach($category->id);

        $url = route('api.v2.articles.latest');

        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonPath('data.0.id', $article->id);
        $res->assertJsonPath('data.0.title', $article->title);
        $res->assertJsonPath('data.0.post_type', $article->post_type);
        $res->assertJsonPath('data.0.contents', $article->contents->getDescription());
        $res->assertJsonPath('data.0.url', route('articles.show', $article->slug));
        $res->assertJsonPath('data.0.author', $article->contents->author);
        $res->assertJsonPath('data.0.categories.0.slug', $category->slug);
        $res->assertJsonPath('data.0.categories.0.type', $category->type);
        $res->assertJsonPath('data.0.categories.0.url', route('category', [$category->type, $category->slug]));
        $res->assertJsonPath('data.0.categories.0.api', route('api.v2.articles.category', [$category->id]));
        $res->assertJsonPath('data.0.created_by.name', $user->name);
        $res->assertJsonPath('data.0.created_by.url', route('user', $user->id));
        $res->assertJsonPath('data.0.created_by.api', route('api.v2.articles.user', $user->id));
    }

    public function testVisibilityDraft()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'draft']);
        $url = route('api.v2.articles.latest');

        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }
    public function testVisibilityPrivate()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'private']);
        $url = route('api.v2.articles.latest');

        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }
    public function testVisibilityTrash()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'trash']);
        $url = route('api.v2.articles.latest');

        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

}
