<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage;

use Closure;
use Illuminate\Http\UploadedFile;
use Tests\ArticleTestCase;

class VerifiedTest extends ArticleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->attachment = $this->createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
    }

    /**
     * @dataProvider dataVerify
     */
    public function testメール確認が未完了(string $method, Closure $route, bool $need_verify)
    {
        $this->user->fill(['email_verified_at' => null])->save();
        $this->actingAs($this->user);

        $url = Closure::bind($route, $this)();

        if ($need_verify) {
            $response = $this->{$method}($url);
            $response->assertForbidden();
        } else {
            $response = $this->{$method}($url);
            $response->assertStatus(200);
        }
    }

    public function dataVerify()
    {
        yield 'マイページトップ' => ['getJson', fn () => '/api/mypage/user', false];
        yield 'タグ検索（投稿ページ）' => ['getJson', fn () => '/api/mypage/tags', false];
        yield '添付ファイル一覧' => ['getJson', fn () => '/api/mypage/attachments', false];
        yield '投稿記事一覧' => ['getJson', fn () => '/api/mypage/articles', false];
        yield '投稿ページオプション' => ['getJson', fn () => '/api/mypage/options', false];

        yield 'プロフィール更新' => ['postJson', fn () => '/api/mypage/user', true];
        yield 'タグ作成' => ['postJson', fn () => '/api/mypage/tags', true];
        yield '添付ファイル作成' => ['postJson', fn () => '/api/mypage/attachments', true];
        yield '添付ファイル削除' => ['deleteJson', fn () => "/api/mypage/attachments/{$this->attachment->id}", true];
        yield '記事投稿' => ['postJson', fn () => '/api/mypage/articles', true];
        yield '記事更新' => ['postJson', fn () => "/api/mypage/articles/{$this->article->id}", true];
    }

    /**
     * @dataProvider dataVerified
     */
    public function testメール確認が完了(string $method, Closure $route, int $expected_status)
    {
        $this->actingAs($this->user);

        $url = Closure::bind($route, $this)();

        $response = $this->{$method}($url);
        $response->assertStatus($expected_status);
    }

    public function dataVerified()
    {
        yield 'プロフィール更新' => ['postJson', fn () => '/api/mypage/user', 422];
        yield 'タグ作成' => ['postJson', fn () => '/api/mypage/tags', 422];
        yield '添付ファイル作成' => ['postJson', fn () => '/api/mypage/attachments', 422];
        yield '添付ファイル削除' => ['deleteJson', fn () => "/api/mypage/attachments/{$this->attachment->id}", 200];
        yield '記事投稿' => ['postJson', fn () => '/api/mypage/articles', 422];
        yield '記事更新' => ['postJson', fn () => "/api/mypage/articles/{$this->article->id}", 422];
    }
}
