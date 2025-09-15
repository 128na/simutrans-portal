<?php

declare(strict_types=1);

namespace App\Events\Article;

use App\Models\Article;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class DeadLinkDetected
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Article $article,
    ) {}
}
