<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Enums\ArticlePostType;
use App\Events\Article\DeadLinkDetected;
use App\Listeners\Article\OnDeadLinkDetected;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\Contents\MarkdownContent;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class OnDeadLinkDetectedTest extends TestCase
{
    #[Test]
    public function it_handles_addon_introduction_article(): void
    {
        $user = User::factory()->make(['id' => 1, 'nickname' => 'testuser']);

        $article = new Article;
        $article->id = 100;
        $article->title = 'テスト記事';
        $article->slug = 'test-article';
        $article->user_id = 1;
        $article->post_type = ArticlePostType::AddonIntroduction;
        $article->setRelation('user', $user);
        $article->contents = new AddonIntroductionContent([
            'link' => 'https://example.com/deadlink',
        ]);

        $listener = app(OnDeadLinkDetected::class);
        $event = new DeadLinkDetected($article);

        // エラーが発生しないことを確認
        $listener->handle($event);

        $this->assertTrue(true);
    }

    #[Test]
    public function it_handles_non_addon_introduction_article(): void
    {
        $user = User::factory()->make(['id' => 1]);

        $article = new Article;
        $article->id = 200;
        $article->title = 'マークダウン記事';
        $article->slug = 'markdown-article';
        $article->user_id = 1;
        $article->post_type = ArticlePostType::Markdown;
        $article->setRelation('user', $user);
        $article->contents = new MarkdownContent([
            'markdown' => '# テスト',
        ]);

        $listener = app(OnDeadLinkDetected::class);
        $event = new DeadLinkDetected($article);

        // エラーが発生しないことを確認（何もログに書き込まれない）
        $listener->handle($event);

        $this->assertTrue(true);
    }

    #[Test]
    public function it_handles_article_with_null_nickname(): void
    {
        $user = User::factory()->make(['id' => 999, 'nickname' => null]);

        $article = new Article;
        $article->id = 300;
        $article->title = 'ニックネームなし記事';
        $article->slug = 'no-nickname-article';
        $article->user_id = 999;
        $article->post_type = ArticlePostType::AddonIntroduction;
        $article->setRelation('user', $user);
        $article->contents = new AddonIntroductionContent([
            'link' => 'https://example.com/test',
        ]);

        $listener = app(OnDeadLinkDetected::class);
        $event = new DeadLinkDetected($article);

        // エラーが発生しないことを確認
        $listener->handle($event);

        $this->assertTrue(true);
    }
}
