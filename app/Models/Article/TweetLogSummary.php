<?php

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TweetLogSummary extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'article_id';

    protected $fillable = [
        'article_id',
        'total_retweet_count',
        'total_reply_count',
        'total_like_count',
        'total_quote_count',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
