<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $article_id
 * @property string|null $text
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read Article|null $article
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleSearchIndex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleSearchIndex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleSearchIndex query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperArticleSearchIndex
 */
class ArticleSearchIndex extends Model
{
    public $incrementing = false;

    protected $table = 'article_search_index';

    protected $primaryKey = 'article_id';

    protected $fillable = [
        'article_id',
        'text',
    ];

    /**
     * @return BelongsTo<Article,$this>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
