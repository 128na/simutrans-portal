<?php

declare(strict_types=1);

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
        'total_impression_count',
        'total_url_link_clicks',
        'total_user_profile_clicks',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
