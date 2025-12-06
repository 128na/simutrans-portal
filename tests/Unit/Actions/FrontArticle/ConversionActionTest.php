<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\FrontArticle;

use App\Actions\FrontArticle\ConversionAction;
use App\Events\ArticleConversion;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\Unit\TestCase;

final class ConversionActionTest extends TestCase
{
    public function test_dispatches_conversion_event_for_guest(): void
    {
        Event::fake();

        $article = Article::factory()->make(['id' => 1]);

        $action = new ConversionAction();
        $action($article, null);

        Event::assertDispatched(ArticleConversion::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }

    public function test_dispatches_conversion_event_for_other_user(): void
    {
        Event::fake();

        $author = User::factory()->make(['id' => 1]);
        $otherUser = User::factory()->make(['id' => 2]);

        $article = Article::factory()->make(['id' => 1, 'user_id' => $author->id]);

        $action = new ConversionAction();
        $action($article, $otherUser);

        Event::assertDispatched(ArticleConversion::class);
    }

    public function test_does_not_dispatch_event_for_article_author(): void
    {
        Event::fake();

        $author = User::factory()->make(['id' => 1]);

        $article = Article::factory()->make(['id' => 1, 'user_id' => $author->id]);

        $action = new ConversionAction();
        $action($article, $author);

        Event::assertNotDispatched(ArticleConversion::class);
    }
}
