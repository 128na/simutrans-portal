<?php

namespace App\Listeners;

use App\Events\ArticleConversion;
use App\Repositories\Article\ConversionCountRepository;

class AddConversionRecord
{
    private ConversionCountRepository $conversionCountRepository;

    public function __construct(ConversionCountRepository $conversionCountRepository)
    {
        $this->conversionCountRepository = $conversionCountRepository;
    }

    public function handle(ArticleConversion $event)
    {
        $this->conversionCountRepository->countUp($event->article);
    }
}
