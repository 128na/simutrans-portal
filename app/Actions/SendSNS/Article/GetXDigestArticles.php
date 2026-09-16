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
 * 対象: status=publish かつ (published_at が cutoff より後 または modified_at が cutoff より後)、
 * いずれも until 以下。
 *
 * ADR-0005の方針により、新規公開と更新を型でグルーピングせず、イベント時刻
 * (新規はpublished_at、更新はmodified_at)で単純に降順マージしてから上位3件を返す。
 */
class GetXDigestArticles
{
    private const int LIMIT = 3;

    public function __construct(private Article $article) {}

    public function __invoke(CarbonImmutable $cutoff, CarbonImmutable $until): XDigestArticles
    {
        $articles = $this->article->newQuery()
            ->where('status', ArticleStatus::Publish)
            ->where(function (Builder $query) use ($cutoff, $until): void {
                $query->where(function (Builder $query) use ($cutoff, $until): void {
                    $query->where('published_at', '>', $cutoff)
                        ->where('published_at', '<=', $until);
                })->orWhere(function (Builder $query) use ($cutoff, $until): void {
                    $query->where('modified_at', '>', $cutoff)
                        ->where('modified_at', '<=', $until);
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
        /** @var CarbonImmutable|null $publishedAt */
        $publishedAt = $article->published_at;
        /** @var CarbonImmutable|null $modifiedAt */
        $modifiedAt = $article->modified_at;

        if ($publishedAt !== null && $publishedAt->gt($cutoff)) {
            return new XDigestArticle($article, XDigestArticleType::Publish, $publishedAt);
        }

        return new XDigestArticle($article, XDigestArticleType::Update, $modifiedAt ?? $cutoff);
    }
}
