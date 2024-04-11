<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Events\Article\DeadLinkDetected;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Repositories\ArticleRepository;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Sleep;

final class Check
{
    private bool $changeAnyArticle = false;

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly FailedCountCache $failedCountCache,
        private readonly GetHeaders $getHeaders,
    ) {

    }

    /**
     * @param  callable(Article $article):bool  $onDead
     */
    public function __invoke(callable $onDead): void
    {
        foreach ($this->getArticles() as $article) {
            if ($this->shouldProcess($article)) {
                if ($this->isDead($article) === false) {
                    $this->failedCountCache->clear($article);

                    continue;
                }

                $changed = $onDead($article);
                if (! $this->changeAnyArticle && $changed) {
                    $this->changeAnyArticle = true;
                }
            }
        }

        if ($this->changeAnyArticle) {
            JobUpdateRelated::dispatchSync();
        }
    }

    /**
     * @return LazyCollection<int,Article>
     */
    private function getArticles(): LazyCollection
    {
        return $this->articleRepository->cursorCheckLink();
    }

    private function shouldProcess(Article $article): bool
    {
        assert($article->contents instanceof AddonIntroductionContent);

        return $article->contents->link
            && $this->inBlacklist($article->contents->link) === false
            && $article->contents->exclude_link_check === false;
    }

    private function inBlacklist(string $url): bool
    {
        $blackList = [
            'getuploader.com',
        ];

        foreach ($blackList as $b) {
            if (stripos($url, $b) !== false) {
                logger('[DeadLinkChecker] blacklist url', [$url]);

                return true;
            }
        }

        return false;
    }

    private function isDead(Article $article, int $retry = 3, int $intervalsec = 1): bool
    {
        assert($article->contents instanceof AddonIntroductionContent);
        for ($i = 0; $i < $retry; $i++) {
            if ($article->contents->link !== null && $article->contents->link !== '' && $article->contents->link !== '0') {
                $info = ($this->getHeaders)($article->contents->link);
                foreach ($info as $inf) {
                    if (stripos($inf, '200 OK') !== false) {
                        return false;
                    }
                }

                logger('[DeadLinkChecker] status check failed.', [$article->contents->link, ...$info]);
            }

            Sleep::for($intervalsec)->second();
        }

        DeadLinkDetected::dispatch($article);

        return true;
    }
}
