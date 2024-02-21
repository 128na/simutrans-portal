<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\ArticleShown;
use App\Repositories\Article\ViewCountRepository;

class AddViewRecord
{
    public function __construct(private readonly ViewCountRepository $viewCountRepository)
    {
    }

    public function handle(ArticleShown $event): void
    {
        $this->viewCountRepository->countUp($event->article);
    }
}
