<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
