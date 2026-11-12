<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 一覧の「人気順」の順位（記事ごとに 1 行）。article:refresh-article-popularities が毎時作り直す（数えるのは前日までの日次）
 *
 * @property int $article_id
 * @property int $rank 人気順の順位（1 から。CV 合計・公開日時・id の降順）
 * @property int $conversion_count 前日までの直近90日のCV合計
 * @property-read Article|null $article
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticlePopularity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticlePopularity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticlePopularity query()
 *
 * @mixin \Eloquent
 */
class ArticlePopularity extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'article_id';

    protected $fillable = [
        'article_id',
        'rank',
        'conversion_count',
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
            'rank' => 'integer',
            'conversion_count' => 'integer',
        ];
    }
}
