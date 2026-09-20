<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\XDigestLogStatus;
use Carbon\CarbonImmutable;
use Database\Factories\XDigestLogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * X (Twitter) 日次集約投稿(sns:x-daily-digest)の実行履歴。
 * 投稿前に status=pending で作成し、投稿結果に応じて success/failed へ更新する。
 * status=success の cutoff_at のみが次回実行の対象期間の起点として利用される。
 *
 * @property int $id
 * @property CarbonImmutable $cutoff_at 今回の実行がカバーする対象期間の終端（成功時のみ次回のcutoffになる）
 * @property int $article_count 投稿に含めた対象記事の総数（表示3件+ほかN件の合計）
 * @property XDigestLogStatus $status 実行結果
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static XDigestLogFactory factory($count = null, $state = [])
 * @method static Builder<static> newModelQuery()
 * @method static Builder<static> newQuery()
 * @method static Builder<static> query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperXDigestLog
 */
class XDigestLog extends Model
{
    /** @use HasFactory<XDigestLogFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'cutoff_at',
        'article_count',
        'status',
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
            'status' => XDigestLogStatus::class,
        ];
    }
}
