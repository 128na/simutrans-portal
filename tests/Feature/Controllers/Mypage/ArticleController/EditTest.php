<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\ArticleController;

use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

final class EditTest extends TestCase
{
    private User $user;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->article = Article::factory()
            ->for($this->user)
            ->addonIntroduction()
            ->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/mypage/articles/edit/' . $this->article->id;

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時に記事編集ページを表示(): void
    {
        $url = '/mypage/articles/edit/' . $this->article->id;

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_他のユーザーの記事は編集できない(): void
    {
        $otherUser = User::factory()->create();
        $otherArticle = Article::factory()
            ->for($otherUser)
            ->addonIntroduction()
            ->create();

        $url = '/mypage/articles/edit/' . $otherArticle->id;

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertForbidden();
    }

    public function test_編集対象の記事情報が正しく渡される(): void
    {
        $url = '/mypage/articles/edit/' . $this->article->id;

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
