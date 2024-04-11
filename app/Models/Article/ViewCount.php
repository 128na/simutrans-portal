<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperViewCount
 */
final class ViewCount extends Model
{
    public const int TYPE_DAILY = 1;

    public const int TYPE_MONTHLY = 2;

    public const int TYPE_YEARLY = 3;

    public const int TYPE_TOTAL = 4;

    public $timestamps = false;

    protected $fillable = [
        'article_id',
        'type',
        'period',
        'count',
    ];

    protected $casts = [
        'count' => 'integer',
    ];

    /**
     * @return BelongsTo<Article,ViewCount>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
