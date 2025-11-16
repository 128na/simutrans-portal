<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperArticleSearchIndex
 */
final class ArticleSearchIndex extends Model
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
