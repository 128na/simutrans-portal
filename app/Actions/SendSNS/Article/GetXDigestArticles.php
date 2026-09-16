<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\Data\XDigestArticle;
use App\Actions\SendSNS\Article\Data\XDigestArticles;
use App\Enums\ArticleStatus;
use App\Enums\XDigestArticleType;
use App\Models\Article;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Xデイリー集約投稿(sns:x-daily-digest)の対象記事を取得する。
 *
 * 対象: status=publish かつ (sns_digest_published_at が cutoff より後 または
 * sns_digest_updated_at が cutoff より後)、いずれも until 以下。
 * これらの日時は OnArticleStored/OnArticleUpdated リスナーが、is_publish かつ
 * shouldNotify(著者の通知希望)を満たし実際に通知した瞬間にのみ刻むため、
 * 通知対象外の更新（shouldNotify=false や、ステータスのみのAPI/MCPツール経由の
 * 更新）は published_at/modified_at が変化してもここには現れない。
 *
 * ADR-0005の方針により、新規公開と更新を型でグルーピングせず、イベント時刻
 * (新規はsns_digest_published_at、更新はsns_digest_updated_at)で単純に降順
 * マージしてから上位3件を返す。
 */
class GetXDigestArticles
{
    private const int LIMIT = 3;

    public function __construct(private Article $article) {}

    public function __invoke(CarbonImmutable $cutoff, CarbonImmutable $until): XDigestArticles
    {
        $articles = $this->article->newQuery()
            ->with('user')
            ->where('status', ArticleStatus::Publish)
            ->where(function (Builder $query) use ($cutoff, $until): void {
                $query->where(function (Builder $query) use ($cutoff, $until): void {
                    $query->where('sns_digest_published_at', '>', $cutoff)
                        ->where('sns_digest_published_at', '<=', $until);
                })->orWhere(function (Builder $query) use ($cutoff, $until): void {
                    $query->where('sns_digest_updated_at', '>', $cutoff)
                        ->where('sns_digest_updated_at', '<=', $until);
                });
            })
            ->get();

        $items = $articles
            ->map(fn (Article $article): XDigestArticle => $this->classify($article, $cutoff))
            ->sortByDesc(fn (XDigestArticle $item): int => $item->eventAt->getTimestamp())
            ->values();

        return new XDigestArticles($items->take(self::LIMIT)->values(), $items->count());
    }

    private function classify(Article $article, CarbonImmutable $cutoff): XDigestArticle
    {
        /** @var CarbonImmutable|null $snsDigestPublishedAt */
        $snsDigestPublishedAt = $article->sns_digest_published_at;
        /** @var CarbonImmutable|null $snsDigestUpdatedAt */
        $snsDigestUpdatedAt = $article->sns_digest_updated_at;

        if ($snsDigestPublishedAt !== null && $snsDigestPublishedAt->gt($cutoff)) {
            return new XDigestArticle($article, XDigestArticleType::Publish, $snsDigestPublishedAt);
        }

        return new XDigestArticle($article, XDigestArticleType::Update, $snsDigestUpdatedAt ?? $cutoff);
    }
}
