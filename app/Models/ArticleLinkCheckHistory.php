<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $article_id
 * @property int $failed_count
 * @property \Carbon\CarbonImmutable $last_checked_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Article $article
 *
 * @method static \Database\Factories\ArticleLinkCheckHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLinkCheckHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLinkCheckHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLinkCheckHistory query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperArticleLinkCheckHistory
 */
class ArticleLinkCheckHistory extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleLinkCheckHistoryFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'article_id',
        'failed_count',
        'last_checked_at',
    ];

    /** @return BelongsTo<Article,$this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @return array<string, string> */
    protected function casts(): array
    {
        return [
            'failed_count' => 'integer',
            'last_checked_at' => 'datetime',
        ];
    }
}
