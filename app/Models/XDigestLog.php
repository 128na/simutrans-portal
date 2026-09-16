<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\XDigestLogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * X (Twitter) 日次集約投稿(sns:x-daily-digest)の成功実行履歴。
 * 成功時のみ1行作成し、cutoff_at を次回実行の対象期間の起点として利用する。
 *
 * @property int $id
 * @property CarbonImmutable $cutoff_at 今回の成功実行がカバーした対象期間の終端（次回のcutoffになる）
 * @property int $article_count 投稿に含めた対象記事の総数（表示3件+ほかN件の合計）
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static XDigestLogFactory factory($count = null, $state = [])
 * @method static Builder<static> newModelQuery()
 * @method static Builder<static> newQuery()
 * @method static Builder<static> query()
 *
 * @mixin \Eloquent
 */
class XDigestLog extends Model
{
    /** @use HasFactory<XDigestLogFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'cutoff_at',
        'article_count',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'cutoff_at' => 'immutable_datetime',
            'article_count' => 'integer',
        ];
    }
}
