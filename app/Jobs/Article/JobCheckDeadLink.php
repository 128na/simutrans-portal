<?php

namespace App\Jobs\Article;

use App\Models\Article;
use App\Notifications\DeadLinkDetected;
use App\Repositories\ArticleRepository;
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

    public function handle(ArticleRepository $articleRepository)
    {
        $changed = false;
        foreach ($articleRepository->cursorCheckLink() as $article) {
            if ($this->isLinkDead($article)) {
                logger('dead link '.$article->title);

                $articleRepository->update($article, [
                    'status' => config('status.private'),
                ]);

                $article->notify(new DeadLinkDetected());
                $changed = true;
                sleep(5);
            }
        }

        if ($changed) {
            JobUpdateRelated::dispatchSync();
        }
    }

    private function isLinkDead(Article $article): bool
    {
        $link = $article->contents->link ?? null;

        if ($link) {
            return !$this->isStatusOK($link);
        }

        return false;
    }

    private function isStatusOK(string $url, int $retry = 3): bool
    {
        for ($i = 0; $i < $retry; ++$i) {
            $info = @get_headers($url) ?: [];
            foreach ($info as $i) {
                if (stripos($i, '200 OK') !== false) {
                    return true;
                }
            }
            logger('status check', [$url, ...$info]);
            sleep(5);
        }

        return false;
    }
}
