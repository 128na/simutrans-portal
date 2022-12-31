<?php

declare(strict_types=1);

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

    public function handle(ArticleConversion $event): void
    {
        $this->conversionCountRepository->countUp($event->article);
    }
}
