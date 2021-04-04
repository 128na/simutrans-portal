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
        yield 'マイページトップ' => ['getJson', fn () => route('api.v2.users.index'), false];
        yield 'タグ検索（投稿ページ）' => ['getJson', fn () => route('api.v2.tags.search'), false];
        yield '添付ファイル一覧' => ['getJson', fn () => route('api.v2.attachments.index'), false];
        yield '投稿記事一覧' => ['getJson', fn () => route('api.v2.articles.index'), false];
        yield '投稿ページオプション' => ['getJson', fn () => route('api.v2.articles.options'), false];

        yield 'プロフィール更新' => ['postJson', fn () => route('api.v2.users.update'), true];
        yield 'タグ作成' => ['postJson', fn () => route('api.v2.tags.store'), true];
        yield '添付ファイル作成' => ['postJson', fn () => route('api.v2.attachments.store'), true];
        yield '添付ファイル削除' => ['deleteJson', fn () => route('api.v2.attachments.destroy', $this->attachment), true];
        yield '記事投稿' => ['postJson', fn () => route('api.v2.articles.store'), true];
        yield '記事更新' => ['postJson', fn () => route('api.v2.articles.update', $this->article), true];
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
        yield 'プロフィール更新' => ['postJson', fn () => route('api.v2.users.update'), 422];
        yield 'タグ作成' => ['postJson', fn () => route('api.v2.tags.store'), 422];
        yield '添付ファイル作成' => ['postJson', fn () => route('api.v2.attachments.store'), 422];
        yield '添付ファイル削除' => ['deleteJson', fn () => route('api.v2.attachments.destroy', $this->attachment), 200];
        yield '記事投稿' => ['postJson', fn () => route('api.v2.articles.store'), 422];
        yield '記事更新' => ['postJson', fn () => route('api.v2.articles.update', $this->article), 422];
    }
}
