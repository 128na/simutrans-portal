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

    public function handle(ArticleShown $articleShown): void
    {
        $this->viewCountRepository->countUp($articleShown->article);
    }
}
