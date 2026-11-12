<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * 公開側のアドオン一覧の並び順
 */
enum ArticleListSort: string
{
    /**
     * 更新が新しい順（既定）
     */
    case Modified = 'modified';

    /**
     * 公開が新しい順
     */
    case Published = 'published';

    /**
     * 直近 90 日の入手数（ダウンロードと掲載先クリック）が多い順。
     * 並びは記事本体ではなく article_popularities の順位で、1 日 1 回作り直す（article:refresh-article-popularities）
     */
    case Popular = 'popular';

    /**
     * 降順に並べる articles の列。人気順は articles の列ではなく順位表で並べるので null
     */
    public function column(): ?string
    {
        return match ($this) {
            self::Modified => 'articles.modified_at',
            self::Published => 'articles.published_at',
            self::Popular => null,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Modified => '更新が新しい順',
            self::Published => '公開が新しい順',
            self::Popular => '人気順',
        };
    }

    /**
     * 並び順の補足（選んだときだけ一覧の上に出す）
     */
    public function note(): ?string
    {
        return match ($this) {
            self::Modified, self::Published => null,
            self::Popular => '人気順：直近 90 日の入手数（ダウンロードと掲載先クリック）の目安。1 日 1 回更新',
        };
    }
}
