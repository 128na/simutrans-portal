<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Sleep;

class Check
{
    private const int FAILED_LIMIT = 3;

    private const int INTERVAL_SEC = 1;

    private bool $changeAnyArticle = false;

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly ArticleLinkCheckHistoryRepository $articleLinkCheckHistoryRepository,
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
                $isDead = $this->isDead($article);

                if ($isDead === null) {
                    // 判定不能（一度も応答を受け取れなかった）。既存の履歴に手を付けない。
                    continue;
                }

                if ($isDead === false) {
                    $this->articleLinkCheckHistoryRepository->clear($article);

                    continue;
                }

                $changed = $onDead($article);
                if (! $this->changeAnyArticle && $changed) {
                    $this->changeAnyArticle = true;
                }
            }
        }

        if ($this->changeAnyArticle) {
            dispatch_sync(new JobUpdateRelated);
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
        if (! $article->contents instanceof AddonIntroductionContent) {
            return false;
        }

        return $article->contents->link
            && ($this->inIgnoreList)($article->contents->link) === false
            && $article->contents->exclude_link_check === false;
    }

    /**
     * リンク切れかどうかを判定する。
     *
     * 戻り値は3値:
     * - true: リンク切れ確定（200 OK以外の応答を受け取り続けた）
     * - false: 生存確認済み（200 OKを受信した）
     * - null: 判定不能（FAILED_LIMIT回とも一度も応答を受け取れなかった。
     *   接続不能を「生存」と同一視して履歴をクリアしないよう、呼び出し元で
     *   このケースは何もせずスキップする）
     */
    private function isDead(Article $article): ?bool
    {
        if (! $article->contents instanceof AddonIntroductionContent) {
            return false;
        }

        $receivedResponse = false;

        for ($i = 0; $i < self::FAILED_LIMIT; $i++) {
            if (! in_array($article->contents->link, [null, '', '0'], true)) {
                $info = ($this->getHeaders)($article->contents->link);

                if ($info === null) {
                    logger('[DeadLinkChecker] could not connect.', [$article->contents->link]);
                } else {
                    $receivedResponse = true;

                    foreach ($info as $inf) {
                        if (mb_stripos($inf, '200 OK') !== false) {
                            return false;
                        }
                    }

                    logger('[DeadLinkChecker] status check failed.', [$article->contents->link, ...$info]);
                }
            }

            if ($i < self::FAILED_LIMIT - 1) {
                Sleep::for(self::INTERVAL_SEC)->second();
            }
        }

        return $receivedResponse ? true : null;
    }
}
