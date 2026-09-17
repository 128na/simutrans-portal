<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

class UserControllerTest extends TestCase
{
    public function test_users(): void
    {
        $testResponse = $this->get(route('users.index'));

        $testResponse->assertOk();
    }

    public function test_user(): void
    {
        $user = User::factory()->create();
        $testResponse = $this->get(route('users.show', ['userIdOrNickname' => $user->id]));
        $testResponse->assertOk();

        $testResponse = $this->get(route('users.show', ['userIdOrNickname' => $user->nickname]));
        $testResponse->assertOk();
    }

    /**
     * ArticleList リソースの `url` フィールドが日本語スラッグでも二重エンコードされず、
     * 実際にアクセス可能なURLとして出力されることを確認する。
     */
    public function test_user_記事一覧のurlが日本語スラッグでも二重エンコードされない(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->addonPost()->publish()->create([
            'user_id' => $user->id,
            'title' => '日本語のアドオン紹介記事',
            'slug' => '日本語のアドオン紹介記事',
        ]);

        $testResponse = $this->get(route('users.show', ['userIdOrNickname' => $user->id]));
        $testResponse->assertOk();

        preg_match('#<script id="data-articles" type="application/json">(.*?)</script>#s', (string) $testResponse->getContent(), $matches);
        $this->assertArrayHasKey(1, $matches, 'data-articles の埋め込みJSONが見つかりません');

        /** @var array<int, array{url: string}> $data */
        $data = json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR);
        $url = $data[0]['url'];

        $this->assertStringNotContainsString('%25', $url);
        $this->assertSame($article->showUrl(), $url);

        $this->get($url)->assertOk();
    }
}
