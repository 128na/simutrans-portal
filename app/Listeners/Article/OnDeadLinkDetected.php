<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\Article\DeadLinkDetectd;
use App\Listeners\BaseListener;
use App\Models\Contents\AddonIntroductionContent;
use Illuminate\Log\Logger;

class OnDeadLinkDetected extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(DeadLinkDetectd $event): void
    {
        $article = $event->article;
        $contents = $article->contents;
        assert($contents instanceof AddonIntroductionContent);

        $this->logger->channel('audit')->warning('リンク切れ検知', [
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'articleUrl' => route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]),
            'descUrl' => $contents->link,
        ]);
    }
}
