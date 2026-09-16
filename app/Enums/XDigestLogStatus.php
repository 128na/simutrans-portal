<?php

declare(strict_types=1);

namespace App\Enums;

enum XDigestLogStatus: string
{
    /**
     * X への投稿を試行中（投稿API呼び出し前に書き込み、成功/失敗で更新される）
     */
    case Pending = 'pending';

    /**
     * 投稿成功（または対象0件で投稿不要だった正常終了）
     */
    case Success = 'success';

    /**
     * 投稿失敗（cutoffは進めない）
     */
    case Failed = 'failed';
}
