<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article\Data;

use App\Enums\XDigestArticleType;
use App\Models\Article;
use Carbon\CarbonImmutable;

/**
 * Xデイリー集約投稿の対象記事1件を表すDTO。
 * eventAt は type に応じて published_at(新規) / modified_at(更新) のいずれかであり、
 * タイムライン上のマージ順（イベント時刻降順）を決定するために使う。
 */
final readonly class XDigestArticle
{
    public function __construct(
        public Article $article,
        public XDigestArticleType $type,
        public CarbonImmutable $eventAt,
    ) {}
}
