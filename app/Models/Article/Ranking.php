<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperRanking
 */
final class Ranking extends Model
{
    /** @use HasFactory<\Database\Factories\Article\RankingFactory> */
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'rank',
        'article_id',
    ];

    /**
     * @return BelongsTo<Article,$this>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
