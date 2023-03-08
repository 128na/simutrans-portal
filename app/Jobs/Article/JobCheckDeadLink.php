<?php

declare(strict_types=1);

namespace App\Jobs\Article;

use App\Services\Article\DeadLinkChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCheckDeadLink implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(DeadLinkChecker $deadLinkChecker): void
    {
        $changeAnyArticle = false;
        foreach ($deadLinkChecker->getArticles() as $article) {
            if ($deadLinkChecker->shouldProcess($article)) {
                if ($deadLinkChecker->isDead($article)) {
                    $count = $deadLinkChecker->getFailedCount($article) + 1;
                    if ($count >= 3) {
                        $deadLinkChecker->closeArticle($article);
                        $deadLinkChecker->clearFailedCount($article);
                        $changeAnyArticle = true;
                    } else {
                        $deadLinkChecker->updateFailedCount($article, $count);
                    }
                }
            }
        }

        if ($changeAnyArticle) {
            JobUpdateRelated::dispatchSync();
        }
    }
}
