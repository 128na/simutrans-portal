<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

/**
 * 記事詳細の「編集」ボタン（#688）。
 * 記事の持ち主がログインして見ているときだけ、マイページの編集画面へのリンクを出す。
 */
class ShowEditLinkTest extends TestCase
{
    private Article $article;

    private User $other;

    protected function setUp(): void
    {
        parent::setUp();

        // 記事の id と、持ち主・他人の user id が重ならないように先にユーザーを作っておく
        $this->other = User::factory()->create();
        User::factory()->count(3)->create();
        $this->article = Article::factory()->publish()->create();

        $this->assertNotSame($this->article->id, $this->article->user_id);
        $this->assertNotSame($this->article->user_id, $this->other->id);
    }

    public function test_持ち主には編集画面へのリンクを出す(): void
    {
        $owner = $this->article->user;
        $this->assertInstanceOf(User::class, $owner);

        $html = $this->actingAs($owner)->showHtml();

        $this->assertStringContainsString($this->editHref(), $html);
    }

    public function test_他人には編集画面へのリンクを出さない(): void
    {
        $html = $this->actingAs($this->other)->showHtml();

        $this->assertStringNotContainsString($this->editHref(), $html);
    }

    public function test_未ログインには編集画面へのリンクを出さない(): void
    {
        $this->assertStringNotContainsString($this->editHref(), $this->showHtml());
    }

    private function showHtml(): string
    {
        $html = $this->get(route('articles.show', [
            'userIdOrNickname' => $this->article->user_id,
            'articleSlug' => $this->article->slug,
        ]))->assertOk()->getContent();
        $this->assertIsString($html);

        return $html;
    }

    private function editHref(): string
    {
        return 'href="'.e(route('mypage.articles.edit', $this->article)).'"';
    }
}
