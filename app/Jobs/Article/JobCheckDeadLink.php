<?php

declare(strict_types=1);

namespace App\Jobs\Article;

use App\Models\Article;
use App\Services\Article\DeadLinkChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class JobCheckDeadLink implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private bool $changeAnyArticle = false;

    public function handle(DeadLinkChecker $deadLinkChecker): void
    {
        foreach ($deadLinkChecker->getArticles() as $article) {
            if ($deadLinkChecker->shouldProcess($article)) {
                $this->handleProcess($deadLinkChecker, $article);
            }
        }

        if ($this->changeAnyArticle) {
            JobUpdateRelated::dispatchSync();
        }
    }

    private function handleProcess(DeadLinkChecker $deadLinkChecker, Article $article): void
    {
        if ($deadLinkChecker->isDead($article) === false) {
            $deadLinkChecker->clearFailedCount($article);

            return;
        }

        $this->handleDead($deadLinkChecker, $article);
    }

    private function handleDead(DeadLinkChecker $deadLinkChecker, Article $article): void
    {
        $count = 1 + $deadLinkChecker->getFailedCount($article);
        if ($count < 3) {
            $deadLinkChecker->updateFailedCount($article, $count);

            return;
        }

        $deadLinkChecker->closeArticle($article);
        $deadLinkChecker->clearFailedCount($article);

        $this->changeAnyArticle = true;
    }
}
