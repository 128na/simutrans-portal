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

    public function column(): string
    {
        return match ($this) {
            self::Modified => 'articles.modified_at',
            self::Published => 'articles.published_at',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Modified => '更新が新しい順',
            self::Published => '公開が新しい順',
        };
    }
}
