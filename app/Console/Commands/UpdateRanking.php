<?php

namespace App\Console\Commands;

use App\Services\GoogleAnalytics\ConvertReportResponseService;
use App\Services\GoogleAnalytics\RankingService;
use App\Services\GoogleAnalytics\ReportingService;
use App\Services\GoogleAnalytics\ResolveArticleService;
use Illuminate\Console\Command;

class UpdateRanking extends Command
{
    protected $signature = 'ranking:update';

    protected $description = 'GAデータからランキングを生成する';

    public function __construct(
        private ReportingService $reportingService,
        private ConvertReportResponseService $convertReportResponseService,
        private ResolveArticleService $resolveArticleService,
        private RankingService $rankingService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $viewId = config('analytics.view_id');
            $response = $this->reportingService->getPageViewRanking($viewId, '1daysAgo');
            $ranking = $this->convertReportResponseService->rankingResponseToArray($response);
            $articles = $this->resolveArticleService->pathToArticles(array_keys($ranking));
            $this->rankingService->updateRanking($articles);

            $this->info('success');

            return 0;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            report($e);

            return 1;
        }
    }
}
