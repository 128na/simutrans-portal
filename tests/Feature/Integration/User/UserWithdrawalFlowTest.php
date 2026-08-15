<?php

declare(strict_types=1);

namespace Tests\Feature\Integration\User;

use App\Actions\Article\Data\StoreArticleData;
use App\Actions\Article\StoreArticle;
use App\Actions\User\DeleteAccount;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\User;
use App\Repositories\Article\FrontArticleRepository;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\TestCase;

/**
 * ユーザー退会統合テスト
 * ログイン → 記事投稿 → 退会 → 記事非表示・再ログイン不可の一連の流れを検証
 */
class UserWithdrawalFlowTest extends TestCase
{
    public function test_退会後は記事が公開一覧から消え再ログインできない(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $storeArticleAction = app(StoreArticle::class);
        $article = $storeArticleAction($user, StoreArticleData::fromArray([
            'should_notify' => false,
            'article' => [
                'status' => ArticleStatus::Publish->value,
                'title' => 'Withdrawal Flow Article',
                'slug' => 'withdrawal-flow-article',
                'post_type' => ArticlePostType::Markdown->value,
                'contents' => ['markdown' => '# Hello World'],
            ],
        ]));

        $frontArticleRepository = app(FrontArticleRepository::class);
        $this->assertNotNull($frontArticleRepository->first((string) $user->id, $article->slug));

        $deleteAccount = app(DeleteAccount::class);
        $deleteAccount($user);

        $this->assertNull($frontArticleRepository->first((string) $user->id, $article->slug));

        Auth::logout();
        $canLogin = Auth::attempt(['email' => $user->email, 'password' => 'password']);
        $this->assertFalse($canLogin);
        $this->assertNull(Auth::user());
    }
}
