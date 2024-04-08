<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateSitemap\Refresh;
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
    public function handle(Refresh $refresh)
    {
        try {
            $siteurl = config('app.url');
            $refresh($siteurl);
        } catch (\Throwable $th) {
            report($th);
            $this->error($th->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
