<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Article;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\TestCase;

class ArticleTest extends TestCase
{
    #[Test]
    public function showurlで生成したurlに日本語タイトル記事でも実際にアクセスできる(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->publish()->create([
            'user_id' => $user->id,
            'title' => '日本語タイトルの記事です',
            'slug' => '日本語タイトルの記事です',
        ]);

        $url = $article->showUrl();
        $this->assertStringNotContainsString('%25', $url);

        $testResponse = $this->get($url);

        $testResponse->assertOk();
    }
}
