<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $article_id
 * @property int $type 集計区分 1:日次,2:月次,3:年次,4:全体
 * @property string $period 集計期間
 * @property int $count カウント
 * @property int $user_id
 * @property-read Article|null $article
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversionCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversionCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversionCount query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperConversionCount
 */
class ConversionCount extends Model
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

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    /**
     * @return BelongsTo<Article,$this>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
