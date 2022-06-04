<?php

namespace App\Listeners;

use App\Events\ArticleConversion;
use App\Repositories\CoversionCountRepository;

class AddConversionRecord
{
    private CoversionCountRepository $coversionCountRepository;

    public function __construct(CoversionCountRepository $coversionCountRepository)
    {
        $this->coversionCountRepository = $coversionCountRepository;
    }

    public function handle(ArticleConversion $event)
    {
        $this->coversionCountRepository->countUp($event->article);
    }
}
