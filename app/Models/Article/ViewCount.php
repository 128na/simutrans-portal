<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use App\Models\User;
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

    /**
     * @return BelongsTo<Article,$this>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'count' => 'integer',
        ];
    }
    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
