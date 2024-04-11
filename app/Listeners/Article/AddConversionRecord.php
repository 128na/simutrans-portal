<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\ArticleConversion;
use App\Repositories\Article\ConversionCountRepository;

final readonly class AddConversionRecord
{
    public function __construct(private readonly ConversionCountRepository $conversionCountRepository)
    {
    }

    public function handle(ArticleConversion $articleConversion): void
    {
        $this->conversionCountRepository->countUp($articleConversion->article);
    }
}
