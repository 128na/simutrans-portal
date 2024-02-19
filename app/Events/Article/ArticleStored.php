<?php

declare(strict_types=1);

namespace App\Events\Article;

use App\Models\Article;
use Illuminate\Queue\SerializesModels;

class ArticleStored
{
    use SerializesModels;

    public function __construct(public readonly Article $article)
    {
    }
}
