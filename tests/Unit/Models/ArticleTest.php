<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class ArticleTest extends TestCase
{
    #[Test]
    public function showurlは日本語スラッグを二重エンコードせず単一エンコードで生成する(): void
    {
        $user = User::factory()->make([
            'id' => 1,
            'nickname' => 'testuser',
        ]);

        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'title' => 'レンタルサイクル案内所',
            'slug' => 'レンタルサイクル案内所',
        ]);
        $article->setRelation('user', $user);

        $url = $article->showUrl();

        // 二重エンコードの痕跡（%25）が含まれないこと
        $this->assertStringNotContainsString('%25', $url);

        // パスの最終セグメントを1回だけデコードすると元のスラッグ文字列に戻ること
        $path = (string) parse_url($url, PHP_URL_PATH);
        $segments = explode('/', ltrim($path, '/'));
        $decodedSlug = urldecode((string) end($segments));

        $this->assertSame('レンタルサイクル案内所', $decodedSlug);
    }

    #[Test]
    public function showurlはニックネーム未設定ならuser_idを使う(): void
    {
        $user = User::factory()->make([
            'id' => 42,
            'nickname' => null,
        ]);

        $article = Article::factory()->make([
            'id' => 2,
            'user_id' => 42,
            'title' => 'サンプル記事',
            'slug' => 'サンプル記事',
        ]);
        $article->setRelation('user', $user);

        $url = $article->showUrl();

        $this->assertStringContainsString('/users/42/', $url);
    }
}
