<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use Tests\ArticleTestCase;

final class IndexTest extends ArticleTestCase
{
    public function testIndex(): void
    {
        $url = '/api/mypage/articles';

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

    public function testOptions(): void
    {
        $url = '/api/mypage/options';

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
