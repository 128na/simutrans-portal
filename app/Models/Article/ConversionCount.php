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
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount whereType($value)
 * @mixin \Eloquent
 */
class ConversionCount extends Model
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

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    /**
     * @return BelongsTo<Article,ConversionCount>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
