<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Enums\ArticleStatus;
use App\Events\Article\CloseByDeadLinkDetected;
use App\Events\Article\DeadLinkDetected;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Repositories\ArticleRepository;
use App\Services\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\LazyCollection;

class DeadLinkChecker extends Service
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly GetHeadersHandler $getHeadersHandler
    ) {
    }

    /**
     * @return LazyCollection<int, Article>
     */
    public function getArticles(): LazyCollection
    {
        return $this->articleRepository->cursorCheckLink();
    }

    public function shouldProcess(Article $article): bool
    {
        /** @var AddonIntroductionContent */
        $contents = $article->contents;
        $link = $this->getLink($article);

        return $link && $this->inBlacklist($link) === false && $contents->exclude_link_check === false;
    }

    private function getLink(Article $article): ?string
    {
        $contents = $article->contents;
        if ($contents instanceof AddonIntroductionContent) {
            return $contents->link ?? null;
        }

        return null;
    }

    private function inBlacklist(string $url): bool
    {
        $blackList = [
            'getuploader.com',
        ];

        foreach ($blackList as $b) {
            if (stripos($url, $b) !== false) {
                logger('blacklist url', [$url]);

                return true;
            }
        }

        return false;
    }

    public function isDead(Article $article, int $retry = 3, int $intervalsec = 1): bool
    {
        $url = $this->getLink($article) ?? '';
        for ($i = 0; $i < $retry; $i++) {
            $info = $this->getHeadersHandler->getHeaders($url);
            foreach ($info as $inf) {
                if (stripos($inf, '200 OK') !== false) {
                    return false;
                }
            }

            logger('status check failed.', [$url, ...$info]);
            sleep($intervalsec);
        }

        event(new DeadLinkDetected($article));

        return true;
    }

    public function getFailedCount(Article $article): int
    {
        return Cache::get($this->getCacheKey($article), 0);
    }

    public function updateFailedCount(Article $article, int $count): void
    {
        Cache::put($this->getCacheKey($article), $count);
    }

    public function clearFailedCount(Article $article): void
    {
        Cache::forget($this->getCacheKey($article));
    }

    public function closeArticle(Article $article): void
    {
        $this->articleRepository->update($article, ['status' => ArticleStatus::Private]);
        event(new CloseByDeadLinkDetected($article));
    }

    private function getCacheKey(Article $article): string
    {
        return sprintf('dead_link_check_count_%d', $article->id);
    }
}
