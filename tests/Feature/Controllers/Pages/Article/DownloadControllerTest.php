<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Models\Article;
use App\Models\Attachment;
use Tests\Feature\TestCase;

class DownloadControllerTest extends TestCase
{
    public function test_未公開記事はダウンロードできない(): void
    {
        $attachment = Attachment::factory()->create();
        $article = Article::factory()->draft()->addonPost($attachment)->create();

        $testResponse = $this->get(route('articles.download', ['article' => $article]));

        $testResponse->assertNotFound();
    }

    public function test_未公開記事のコンバージョンはリダイレクトされない(): void
    {
        $article = Article::factory()->draft()->addonIntroduction()->create();

        $testResponse = $this->get(route('articles.conversion', ['article' => $article]));

        $testResponse->assertNotFound();
    }

    public function test_公開記事のコンバージョンはリダイレクトされる(): void
    {
        $article = Article::factory()->publish()->addonIntroduction()->create();

        $testResponse = $this->get(route('articles.conversion', ['article' => $article]));

        $testResponse->assertRedirect($article->contents->link);
    }
}
