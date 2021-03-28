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

    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $changed = false;
        foreach ($this->articleRepository->cursorCheckLinkArticles() as $article) {
            if ($this->isLinkDead($article)) {
                logger('dead link '.$article->title);

                $this->articleRepository->update($article, [
                    'status' => config('status.private'),
                ]);

                $article->notify(new DeadLinkDetected());
                $changed = true;
            }
        }

        if ($changed) {
            dispatch_now(app(JobUpdateRelated::class));
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
