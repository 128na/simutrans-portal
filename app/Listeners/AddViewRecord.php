<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ArticleShown;
use App\Repositories\Article\ViewCountRepository;

class AddViewRecord
{
    private ViewCountRepository $viewCountRepository;

    public function __construct(ViewCountRepository $viewCountRepository)
    {
        $this->viewCountRepository = $viewCountRepository;
    }

    public function handle(ArticleShown $event): void
    {
        $this->viewCountRepository->countUp($event->article);
    }
}
