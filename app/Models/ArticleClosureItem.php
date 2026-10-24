<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ArticleClosureReason;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $batch_id
 * @property int $article_id
 * @property string $title
 * @property string $url
 * @property string $edit_url
 * @property ArticleClosureReason $reason
 * @property CarbonImmutable $closed_at
 * @property-read ArticleClosureBatch $batch
 */
class ArticleClosureItem extends Model
{
    protected $fillable = ['batch_id', 'article_id', 'title', 'url', 'edit_url', 'reason', 'closed_at'];

    /** @return BelongsTo<ArticleClosureBatch, $this> */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ArticleClosureBatch::class, 'batch_id');
    }

    protected function casts(): array
    {
        return ['reason' => ArticleClosureReason::class, 'closed_at' => 'immutable_datetime'];
    }
}
