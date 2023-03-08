<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Notifications\DeadLinkDetected;
use App\Repositories\ArticleRepository;
use App\Services\Logging\AuditLogService;
use App\Services\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\LazyCollection;

class DeadLinkChecker extends Service
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private AuditLogService $auditLogService,
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
        $link = $this->getLink($article);

        return $link && $this->inBlacklist($link) === false;
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
            $info = @get_headers($url) ?: [];
            foreach ($info as $i) {
                if (stripos($i, '200 OK') !== false) {
                    return false;
                }
            }
            logger('status check failed.', [$url, ...$info]);
            sleep($intervalsec);
        }
        $this->auditLogService->deadLinkDetected($article);

        return true;
    }

    public function getFailedCount(Article $article): int
    {
        return (int) Cache::get($this->getCacheKey($article), 0);
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
        $this->articleRepository->update($article, ['status' => config('status.private')]);
        $article->notify(new DeadLinkDetected());
    }

    private function getCacheKey(Article $article): string
    {
        return sprintf('dead_link_check_count_%d', $article->id);
    }
}
