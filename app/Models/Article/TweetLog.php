<?php

declare(strict_types=1);

namespace App\Models\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TweetLog extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'article_id',
        'text',
        'retweet_count',
        'reply_count',
        'like_count',
        'quote_count',
        'impression_count',
        'url_link_clicks',
        'user_profile_clicks',
        'tweet_created_at',
    ];

    protected $casts = [
        'tweet_created_at' => 'timestamp',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
