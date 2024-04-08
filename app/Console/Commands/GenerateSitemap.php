<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateSitemap\Create;
use Illuminate\Console\Command;

final class GenerateSitemap extends Command
{
    /**
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    public function handle(Create $create): int
    {
        try {
            $create();
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());
            $this->error($throwable->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
