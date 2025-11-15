<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\ArticleController;

use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

final class CreateTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/mypage/articles/create';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時に記事作成ページを表示(): void
    {
        $url = '/mypage/articles/create';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
        // $testResponse->assertViewIs('v2.mypage.article-create');
        // $testResponse->assertViewHas('user');
        // $testResponse->assertViewHas('attachments');
        // $testResponse->assertViewHas('categories');
        // $testResponse->assertViewHas('tags');
        // $testResponse->assertViewHas('relationalArticles');
        // $testResponse->assertViewHas('meta');
    }
}
