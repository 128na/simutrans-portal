<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article\Data;

use Illuminate\Support\Collection;

/**
 * GetXDigestArticles の結果DTO。
 * items は投稿に掲載する上位3件（イベント時刻降順）、totalCount は対象期間内の
 * 該当記事の総数（「ほかN件」の算出に使う `totalCount - items->count()`）。
 */
final readonly class XDigestArticles
{
    /**
     * @param  Collection<int, XDigestArticle>  $items
     */
    public function __construct(
        public Collection $items,
        public int $totalCount,
    ) {}
}
