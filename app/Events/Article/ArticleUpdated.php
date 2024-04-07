<?php

declare(strict_types=1);

namespace App\Events\Article;

use App\Models\Article;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Article $article,
        public readonly bool $shouldNotify = false,
        public readonly bool $notYetPublished = true,
    ) {
    }
}
