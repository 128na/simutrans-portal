<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\User;
use Tests\Feature\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/articles';

        $response = $this->postJson($url);
        $response->assertUnauthorized();
    }

    public function test(): void
    {
        $url = '/api/mypage/articles';

        $this->actingAs($this->user);

        $response = $this->postJson($url, ['article' => [
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
        ]]);
        $response->assertStatus(200);
    }
}
