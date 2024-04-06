<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
