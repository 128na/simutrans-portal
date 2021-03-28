<?php

namespace App\Listeners;

use App\Events\ArticleShown;
use App\Repositories\ViewCountRepository;

class AddViewRecord
{
    private ViewCountRepository $viewCountRepository;

    public function __construct(ViewCountRepository $viewCountRepository)
    {
        $this->viewCountRepository = $viewCountRepository;
    }

    public function handle(ArticleShown $event)
    {
        $this->viewCountRepository->countUp($event->article);
    }
}
