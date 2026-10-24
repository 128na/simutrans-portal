<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ArticleClosureReason;
use App\Enums\ArticleClosureRunStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property ArticleClosureReason $kind
 * @property ArticleClosureRunStatus $status
 * @property CarbonImmutable $started_at
 * @property CarbonImmutable $heartbeat_at
 * @property CarbonImmutable|null $finalized_at
 * @property-read Collection<int, ArticleClosureBatch> $batches
 */
class ArticleClosureRun extends Model
{
    protected $fillable = ['kind', 'status', 'started_at', 'heartbeat_at', 'finalized_at'];

    /** @return HasMany<ArticleClosureBatch, $this> */
    public function batches(): HasMany
    {
        return $this->hasMany(ArticleClosureBatch::class, 'run_id');
    }

    protected function casts(): array
    {
        return ['kind' => ArticleClosureReason::class, 'status' => ArticleClosureRunStatus::class,
            'started_at' => 'immutable_datetime', 'heartbeat_at' => 'immutable_datetime', 'finalized_at' => 'immutable_datetime'];
    }
}
