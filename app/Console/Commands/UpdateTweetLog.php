<?php

namespace App\Console\Commands;

use App\Services\TwitterAnalytics\AggregateTweetLogService;
use App\Services\TwitterAnalytics\ResolveArticleService;
use App\Services\TwitterAnalytics\SearchTweetService;
use Illuminate\Console\Command;

class UpdateTweetLog extends Command
{
    protected $signature = 'tweet_log:update';

    protected $description = 'Update tweet logs';

    public function __construct(
        private SearchTweetService $searchTweetService,
        private ResolveArticleService $resolveArticleService,
        private AggregateTweetLogService $aggregateTweetLogService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $result = $this->searchTweetService->searchMyTweets();
            $resolved = $this->resolveArticleService->titleToArticles($result);
            $articleIds = $this->aggregateTweetLogService->updateOrCreateTweetLogs($resolved);
            $this->aggregateTweetLogService->updateOrCreateTweetLogSummary($articleIds);
        } catch (\Throwable $e) {
            report($e);

            return 1;
        }

        return 0;
    }
}
