<?php

declare(strict_types=1);

namespace App\Enums;

enum ControllOptionKey: string
{
    /**
     * ログイン
     **/
    case Login = 'LOGIN';

    /**
     * 新規登録
     **/
    case Register = 'REGISTER';

    /**
     * 招待発行
     **/
    case InvitationCode = 'INVITATION_CODE';

    /**
     * 記事追加・編集
     **/
    case ArticleUpdate = 'ARTICLE_UPDATE';

    /**
     * タグ追加・編集
     **/
    case TagUpdate = 'TAG_UPDATE';

    /**
     * スクショ追加・編集
     **/
    case ScreenshotUpdate = 'SCREENSHOT_UPDATE';
}
