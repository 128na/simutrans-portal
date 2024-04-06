<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $article_id
 * @property int $type 集計区分 1:日次,2:月次,3:年次,4:全体
 * @property string $period 集計期間
 * @property int $count カウント
 * @property-read Article $article
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereType($value)
 * @mixin \Eloquent
 */
class ViewCount extends Model
{
    public const TYPE_DAILY = 1;

    public const TYPE_MONTHLY = 2;

    public const TYPE_YEARLY = 3;

    public const TYPE_TOTAL = 4;

    protected $fillable = [
        'article_id',
        'type',
        'period',
        'count',
    ];

    public $timestamps = false;

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
