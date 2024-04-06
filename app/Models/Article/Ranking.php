<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $rank
 * @property int $article_id
 * @property-read Article $article
 *
 * @method static \Database\Factories\Article\RankingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Ranking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ranking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ranking query()
 *
 * @mixin \Eloquent
 */
class Ranking extends Model
{
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'rank',
        'article_id',
    ];

    /**
     * @return BelongsTo<Article,Ranking>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
