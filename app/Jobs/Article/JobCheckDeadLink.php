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
    }

    private function isStatusOK(string $url): bool
    {
        $info_list = @get_headers($url) ?: [];
        foreach ($info_list as $info) {
            if (stripos($info, ' 200 OK') !== false) {
                return true;
            }
        }

        return false;
    }
}
