<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage;

use App\Models\Article;
use App\Models\Screenshot;
use App\Models\Tag;
use App\Models\User;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class VerifiedTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => null]);
    }

    #[DataProvider('data')]
    public function testメール確認必須(string $method, Closure $route): void
    {
        $this->actingAs($this->user);

        $url = $route($this);
        /**
         * @var \Illuminate\Testing\TestResponse
         */
        $response = $this->{$method}($url);
        $response->assertForbidden();
    }

    public static function data(): \Generator
    {
        // API
        // ユーザー
        yield 'プロフィール更新' => ['postJson', fn (self $self): string => '/api/mypage/user'];
        // タグ
        yield 'タグ投稿' => ['postJson', fn (self $self): string => '/api/mypage/tags'];
        yield 'タグ更新' => ['postJson', fn (self $self): string => '/api/mypage/tags/'.Tag::factory()->create()->id];
        // ファイル
        yield '添付ファイル作成' => ['postJson', fn (self $self): string => '/api/mypage/attachments'];
        yield '添付ファイル削除' => ['deleteJson', fn (self $self): string => '/api/mypage/attachments/'.$self->createAttachment($self->user)->id];
        // 記事
        yield '記事投稿' => ['postJson', fn (self $self): string => '/api/mypage/articles'];
        yield '記事更新' => ['postJson', fn (self $self): string => '/api/mypage/articles/'.Article::factory()->create(['user_id' => $self->user->id])->id];
        // 分析
        yield '分析対象一覧' => ['getJson', fn (self $self): string => '/api/mypage/analytics'];
        // 一括DL機能
        yield 'zip作成' => ['getJson', fn (self $self): string => '/api/mypage/bulk-zip'];
        // 招待機能
        yield '招待コード発行' => ['postJson', fn (self $self): string => '/api/mypage/invitation_code'];
        yield '招待コード削除' => ['deleteJson', fn (self $self): string => '/api/mypage/invitation_code'];
        // スクリーンショット機能
        yield 'スクリーンショット登録' => ['postJson', fn (self $self): string => '/api/mypage/screenshots'];
        yield 'スクリーンショット更新' => ['putJson', fn (self $self): string => '/api/mypage/screenshots/'.Screenshot::factory()->create(['user_id' => $self->user->id])->id];
        yield 'スクリーンショット削除' => ['deleteJson', fn (self $self): string => '/api/mypage/screenshots/'.Screenshot::factory()->create(['user_id' => $self->user->id])->id];
    }
}
