<?php

namespace App\Console\Commands\Ranking;

use App\Repositories\Article\RankingRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Console\Command;

class Update extends Command
{
    protected $signature = 'ranking:update';

    protected $description = 'Update rankings';

    public function __construct(
        private ArticleRepository $articleRepository,
        private RankingRepository $rankingRepository,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $ranking = $this->articleRepository->fetchAggregatedRanking(now());

        $this->rankingRepository->recreate($ranking);

        return 0;
    }
}
