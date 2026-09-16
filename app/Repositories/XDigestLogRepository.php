<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\XDigestLog;
use App\Repositories\Concerns\HasCrud;
use Carbon\CarbonImmutable;

class XDigestLogRepository
{
    use HasCrud;

    public function __construct(private readonly XDigestLog $model) {}

    /**
     * 直近の成功実行が記録した cutoff_at (MAX) を取得する。
     * 実行履歴が1件も無い場合(初回実行)は null を返す。
     */
    public function latestCutoff(): ?CarbonImmutable
    {
        $cutoffAt = $this->model->newQuery()->max('cutoff_at');

        return $cutoffAt === null ? null : CarbonImmutable::parse($cutoffAt);
    }
}
