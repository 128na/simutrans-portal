<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Article\MypageArticleRepository;
use Tests\Feature\TestCase;

class GetRecentForMypageTest extends TestCase
{
    private MypageArticleRepository $articleRepository;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->articleRepository = app(MypageArticleRepository::class);
    }

    public function test更新日の新しい順に件数を絞って取得する(): void
    {
        $oldest = Article::factory()->page()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now()->subDays(3),
        ]);
        $newest = Article::factory()->page()->draft()->create([
            'user_id' => $this->user->id,
            'modified_at' => now(),
        ]);
        $middle = Article::factory()->page()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now()->subDay(),
        ]);

        $result = $this->articleRepository->getRecentForMypage($this->user, 2);

        $this->assertSame([$newest->id, $middle->id], $result->pluck('id')->all());
        $this->assertNotContains($oldest->id, $result->pluck('id')->all());
    }

    public function test他人の記事と論理削除された記事とゴミ箱の記事は含めない(): void
    {
        $otherUser = User::factory()->create();
        $mine = Article::factory()->page()->publish()->create(['user_id' => $this->user->id]);
        Article::factory()->page()->publish()->create(['user_id' => $otherUser->id]);
        Article::factory()->page()->publish()->deleted()->create(['user_id' => $this->user->id]);
        Article::factory()->page()->create(['user_id' => $this->user->id, 'status' => ArticleStatus::Trash]);

        $result = $this->articleRepository->getRecentForMypage($this->user, 5);

        $this->assertSame([$mine->id], $result->pluck('id')->all());
    }

    public function test累計_p_vと_c_vを読み込む(): void
    {
        Article::factory()->page()->publish()->create(['user_id' => $this->user->id]);

        $article = $this->articleRepository->getRecentForMypage($this->user, 5)->first();

        $this->assertNotNull($article);
        $this->assertTrue($article->relationLoaded('totalViewCount'));
        $this->assertTrue($article->relationLoaded('totalConversionCount'));
        $this->assertSame($this->user, $article->user);
    }
}
