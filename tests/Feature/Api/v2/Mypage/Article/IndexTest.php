<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        $url = route('api.v2.articles.index');

        $response = $this->getJson($url);
        $response->assertUnauthorized();

        $this->actingAs($this->user);

        $response = $this->getJson($url);
        $response->assertStatus(200);
        $response->assertJson(['data' => [
            [
                'id' => $this->article->id,
                'title' => $this->article->title,
                'slug' => $this->article->slug,
                'status' => $this->article->status,
                'post_type' => $this->article->post_type,
                'contents' => json_decode(json_encode($this->article->contents), true),
                'categories' => $this->article->categories->pluck('id')->toArray(),
                'tags' => $this->article->tags->pluck('name')->toArray(),
                'url' => route('articles.show', $this->article->slug),
            ],
        ]]);
    }

    public function testOptions()
    {
        $url = route('api.v2.articles.options');

        $response = $this->getJson($url);
        $response->assertUnauthorized();

        $this->actingAs($this->user);

        $response = $this->getJson($url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'categories',
            'statuses',
            'post_types',
        ]);
    }
}
