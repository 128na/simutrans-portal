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

    /**
     * @return mixed
     */
    public function handle(Create $create)
    {
        try {
            $create();
        } catch (\Throwable $th) {
            report($th);
            $this->error($th->getMessage());
            $this->error($th->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
