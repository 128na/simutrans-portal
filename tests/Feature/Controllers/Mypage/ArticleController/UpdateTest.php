<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\ArticleController;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use Tests\Feature\TestCase;

final class UpdateTest extends TestCase
{
    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->addonIntroduction()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/articles/'.$this->article->id;

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test(): void
    {
        $url = '/api/v2/articles/'.$this->article->id;

        $this->actingAs($this->article->user);

        $testResponse = $this->postJson($url, ['article' => [
            'post_type' => ArticlePostType::AddonIntroduction->value,
            'status' => ArticleStatus::Publish->value,
            'title' => 'test title ',
            'slug' => 'test-slug',
            'contents' => [
                'thumbnail' => null,
                'author' => 'test auhtor',
                'link' => 'http://example.com',
                'description' => 'test description',
                'thanks' => 'tets thanks',
                'license' => 'test license',
                'agreement' => true,
            ],
            'tags' => [],
            'categories' => [],
            'articles' => [],
            'published_at' => null,
        ]]);
        $testResponse->assertStatus(200);
    }
}
