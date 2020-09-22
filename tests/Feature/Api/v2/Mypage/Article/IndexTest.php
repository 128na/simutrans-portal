<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testIndex()
    {
        $user = User::factory()->create();
        $article = $this->createPage($user);
        $url = route('api.v2.articles.index');

        $response = $this->getJson($url);
        $response->assertUnauthorized();

        $this->actingAs($user);

        $response = $this->getJson($url);
        $response->assertStatus(200);
        $response->assertJson(['data' => [
            [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status,
                'post_type' => $article->post_type,
                'contents' => json_decode(json_encode($article->contents), true),
                'categories' => $article->categories->pluck('id')->toArray(),
                'tags' => $article->tags->pluck('name')->toArray(),
                'url' => route('articles.show', $article->slug),
            ],
        ]]);
    }

    public function testOptions()
    {
        $user = User::factory()->create();
        $url = route('api.v2.articles.options');

        $response = $this->getJson($url);
        $response->assertUnauthorized();

        $this->actingAs($user);

        $response = $this->getJson($url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'categories',
            'statuses',
            'post_types',
        ]);
    }
}
