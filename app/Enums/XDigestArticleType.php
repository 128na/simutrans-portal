<?php

declare(strict_types=1);

namespace App\Enums;

enum XDigestArticleType: string
{
    /**
     * 新規公開（published_at が cutoff より後）
     */
    case Publish = 'publish';

    /**
     * 更新（published_at は cutoff 以前だが modified_at が cutoff より後）
     */
    case Update = 'update';
}
