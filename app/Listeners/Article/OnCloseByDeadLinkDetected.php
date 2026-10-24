<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\CloseByDeadLinkDetected;
use Illuminate\Support\Facades\Log;

class OnCloseByDeadLinkDetected
{
    public function handle(CloseByDeadLinkDetected $closeByDeadLinkDetected): void
    {
        Log::channel('audit')->info('記事非公開化', [
            'articleId' => $closeByDeadLinkDetected->article->id,
            'articleTitle' => $closeByDeadLinkDetected->article->title,
        ]);
    }
}
