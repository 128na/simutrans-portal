<?php

declare(strict_types=1);

namespace App\Console\Commands\Ranking;

use App\Repositories\Article\RankingRepository;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class Update extends Command
{
    protected $signature = 'ranking:update';

    protected $description = 'Update rankings';

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly RankingRepository $rankingRepository,
        private readonly CarbonImmutable $now,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $ranking = $this->articleRepository->fetchAggregatedRanking($this->now);

            $this->rankingRepository->recreate($ranking);
        } catch (\Throwable $throwable) {
            report($throwable);

            return 1;
        }

        return 0;
    }
}
