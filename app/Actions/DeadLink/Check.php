<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Repositories\ArticleRepository;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Sleep;

final class Check
{
    private const int FAILED_LIMIT = 3;

    private const int INTERVAL_SEC = 1;

    private bool $changeAnyArticle = false;

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly FailedCountCache $failedCountCache,
        private readonly InIgnoreList $inIgnoreList,
        private readonly GetHeaders $getHeaders,
    ) {}

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
                if (!$this->changeAnyArticle && $changed) {
                    $this->changeAnyArticle = true;
                }
            }
        }

        if ($this->changeAnyArticle) {
            dispatch_sync(new \App\Jobs\Article\JobUpdateRelated());
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

        return (
            $article->contents->link
            && ($this->inIgnoreList)($article->contents->link) === false
            && $article->contents->exclude_link_check === false
        );
    }

    private function isDead(Article $article): bool
    {
        assert($article->contents instanceof AddonIntroductionContent);
        for ($i = 0; $i < self::FAILED_LIMIT; $i++) {
            if (!in_array($article->contents->link, [null, '', '0'], true)) {
                $info = ($this->getHeaders)($article->contents->link);
                foreach ($info as $inf) {
                    if (mb_stripos($inf, '200 OK') !== false) {
                        return false;
                    }
                }

                logger('[DeadLinkChecker] status check failed.', [$article->contents->link, ...$info]);
            }

            Sleep::for(self::INTERVAL_SEC)->second();
        }

        return true;
    }
}
