<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\ToOneSignal;
use App\Models\Article;
use App\Models\User;
use App\Notifications\SendArticlePublished;
use Berkayk\OneSignal\OneSignalFacade;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class ToOneSignalTest extends TestCase
{
    #[Test]
    public function 通知先urlは日本語スラッグでも二重エンコードされない(): void
    {
        $user = User::factory()->make(['id' => 1, 'nickname' => 'jpuser']);
        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'title' => '日本語のお知らせ記事',
            'slug' => '日本語のお知らせ記事',
        ]);
        $article->setRelation('user', $user);
        $article->setRelation('categories', collect());
        $article->setRelation('categoryPaks', collect());

        OneSignalFacade::shouldReceive('sendNotificationToAll')
            ->once()
            ->withArgs(function (string $message, string $url) use ($article): bool {
                return ! str_contains($url, '%25') && $url === $article->showUrl();
            });

        $action = app(ToOneSignal::class);
        $action($article, new SendArticlePublished($article));

        $this->assertTrue(true);
    }
}
