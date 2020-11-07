<?php

namespace Tests\Feature\Api\v2\Admin;

use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testFetchArticles()
    {
        $url = route('api.v2.admin.articles.index');
        $response = $this->getJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(200);
    }

    public function testUpdateArticles()
    {
        $target_user = User::factory()->create();
        $target_article = Article::factory()->create(['user_id'=>$target_user->id, 'status' => 'publish']);
        $url = route('api.v2.admin.articles.update', $target_article);
        $data = ['article' => ['status'=> 'private']];

        $response = $this->putJson($url, $data);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->putJson($url, $data);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->putJson($url, $data);
        $response->assertStatus(200);
        $this->assertEquals($target_article->fresh()->status, 'private');

        // バリデーション外の項目は更新されないこと
        $data = ['article' => ['title'=> 'update_'.$target_article->title]];
        $response = $this->actingAs($user)->putJson($url, $data);
        $response->assertStatus(200);
        $this->assertEquals($target_article->title, $target_article->fresh()->title);
    }

    public function testDeleteArticle()
    {
        $target_user = User::factory()->create();
        $target_article = Article::factory()->create(['user_id'=>$target_user->id]);
        $this->assertNull($target_article->deleted_at);
        $url = route('api.v2.admin.articles.destroy', $target_article);
        $response = $this->deleteJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->deleteJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->deleteJson($url);
        $response->assertStatus(200);
        $this->assertFalse(is_null($target_article->fresh()->deleted_at));

        $response = $this->actingAs($user)->deleteJson($url);
        $response->assertStatus(200);
        $this->assertNull($target_article->refresh()->deleted_at);
    }
}
