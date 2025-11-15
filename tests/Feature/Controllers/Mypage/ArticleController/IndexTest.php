<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\ArticleController;

use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
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
        $url = '/mypage/articles';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時に記事一覧ページを表示(): void
    {
        Article::factory()
            ->for($this->user)
            ->count(3)
            ->create();

        $url = '/mypage/articles';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_記事がない時も正常に表示(): void
    {
        $url = '/mypage/articles';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_他のユーザーの記事は表示されない(): void
    {
        $otherUser = User::factory()->create();
        Article::factory()->for($otherUser)->create();
        Article::factory()->for($this->user)->create();

        $url = '/mypage/articles';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
