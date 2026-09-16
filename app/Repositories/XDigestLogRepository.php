<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\XDigestLogStatus;
use App\Models\XDigestLog;
use App\Repositories\Concerns\HasCrud;
use Carbon\CarbonImmutable;

class XDigestLogRepository
{
    use HasCrud;

    public function __construct(private readonly XDigestLog $model) {}

    /**
     * 直近の成功実行(status=success)が記録した cutoff_at (MAX) を取得する。
     * pending/failed の行は次回実行のcutoffとして数えない。
     * 実行履歴が1件も無い場合(初回実行)は null を返す。
     */
    public function latestCutoff(): ?CarbonImmutable
    {
        $cutoffAt = $this->model->newQuery()
            ->where('status', XDigestLogStatus::Success)
            ->max('cutoff_at');

        return $cutoffAt === null ? null : CarbonImmutable::parse($cutoffAt);
    }
}
