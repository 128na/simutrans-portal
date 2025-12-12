<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $article_id
 * @property int $failed_count
 * @property \Illuminate\Support\Carbon $last_checked_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Article $article
 *
 * @mixin IdeHelperArticleLinkCheckHistory
 */
final class ArticleLinkCheckHistory extends Model
{
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
