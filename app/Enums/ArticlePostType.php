<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticlePostType: string
{
    /**
     * アドオン投稿
     */
    case AddonPost = 'addon-post';
    /**
     * アドオン紹介
     */
    case AddonIntroduction = 'addon-introduction';
    /**
     * 一般記事
     */
    case Page = 'page';
    /**
     * 一般記事（マークダウン）
     */
    case Markdown = 'markdown';
}
