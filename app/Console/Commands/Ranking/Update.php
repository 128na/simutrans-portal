<?php

namespace App\Console\Commands\Ranking;

use App\Repositories\Article\RankingRepository;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class Update extends Command
{
    protected $signature = 'ranking:update';

    protected $description = 'Update rankings';

    public function __construct(
        private ArticleRepository $articleRepository,
        private RankingRepository $rankingRepository,
        private CarbonImmutable $now,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $ranking = $this->articleRepository->fetchAggregatedRanking($this->now);

            $this->rankingRepository->recreate($ranking);
        } catch (\Throwable $e) {
            report($e);

            return 1;
        }

        return 0;
    }
}
