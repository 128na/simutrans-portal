<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Actions\Ranking\Update;
use Illuminate\Console\Command;

final class UpdateRanking extends Command
{
    protected $signature = 'article:ranking';

    protected $description = 'Update rankings';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(Update $update): int
    {
        try {
            $update();
        } catch (\Throwable $throwable) {
            report($throwable);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
