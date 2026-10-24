<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ArticleClosureBatchStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $run_id
 * @property int $user_id
 * @property ArticleClosureBatchStatus $status
 * @property int $attempts
 * @property CarbonImmutable|null $next_attempt_at
 * @property CarbonImmutable|null $dispatch_lease_until
 * @property CarbonImmutable|null $sending_at
 * @property string|null $sending_token
 * @property CarbonImmutable|null $sent_at
 * @property string|null $last_error
 * @property-read ArticleClosureRun $run
 * @property-read User|null $user
 * @property-read Collection<int, ArticleClosureItem> $items
 */
class ArticleClosureBatch extends Model
{
    protected $fillable = ['run_id', 'user_id', 'status', 'attempts', 'next_attempt_at', 'dispatch_lease_until', 'sending_at', 'sending_token', 'sent_at', 'last_error'];

    /** @return BelongsTo<ArticleClosureRun, $this> */
    public function run(): BelongsTo
    {
        return $this->belongsTo(ArticleClosureRun::class, 'run_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<ArticleClosureItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(ArticleClosureItem::class, 'batch_id');
    }

    protected function casts(): array
    {
        return ['status' => ArticleClosureBatchStatus::class, 'attempts' => 'integer',
            'next_attempt_at' => 'immutable_datetime', 'dispatch_lease_until' => 'immutable_datetime', 'sending_at' => 'immutable_datetime', 'sent_at' => 'immutable_datetime'];
    }
}
