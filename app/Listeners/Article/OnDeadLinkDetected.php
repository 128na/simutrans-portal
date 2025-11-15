<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\DeadLinkDetected;
use App\Models\Contents\AddonIntroductionContent;
use Illuminate\Log\Logger;

final readonly class OnDeadLinkDetected
{
    public function __construct(
        private Logger $logger,
    ) {}

    public function handle(DeadLinkDetected $deadLinkDetected): void
    {
        $article = $deadLinkDetected->article;
        $contents = $article->contents;
        assert($contents instanceof AddonIntroductionContent);

        $this->logger->channel('audit')->warning('リンク切れ検知', [
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'articleUrl' => route('articles.show', [
                'userIdOrNickname' => $article->user->nickname ?? $article->user_id,
                'articleSlug' => $article->slug,
            ]),
            'descUrl' => $contents->link,
        ]);
    }
}
