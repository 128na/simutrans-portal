<?php

namespace Tests\Feature\Api\v2\Article;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
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
        $tag = Tag::factory()->create();
        $article->tags()->attach($tag->id);

        $url = route('api.v2.articles.tag', $tag);

        $res = $this->getJson($url);
        $res->assertStatus(200);
        $res->assertJsonFragment(['title' => $article->title]);
    }

    public function testInvalid()
    {
        $url = route('api.v2.articles.tag', 65535);

        $res = $this->getJson($url);
        $res->assertStatus(404);
    }

    public function testVisibilityDraft()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'draft']);
        $tag = Tag::factory()->create();
        $article->tags()->attach($tag->id);

        $url = route('api.v2.articles.tag', $tag);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

    public function testVisibilityPrivate()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'private']);
        $tag = Tag::factory()->create();
        $article->tags()->attach($tag->id);

        $url = route('api.v2.articles.tag', $tag);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }

    public function testVisibilityTrash()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'status' => 'trash']);
        $tag = Tag::factory()->create();
        $article->tags()->attach($tag->id);

        $url = route('api.v2.articles.tag', $tag);
        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonMissing(['title' => $article->title]);
    }
}
