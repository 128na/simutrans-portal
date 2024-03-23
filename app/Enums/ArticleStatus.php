<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticleStatus: string
{
    /**
     * 公開
     */
    case Publish = 'publish';

    /**
     * 予約投稿
     */
    case Reservation = 'reservation';

    /**
     * 下書き
     */
    case Draft = 'draft';

    /**
     * 非公開
     */
    case Trash = 'trash';

    /**
     * 非公開
     */
    case Private = 'private';
}
