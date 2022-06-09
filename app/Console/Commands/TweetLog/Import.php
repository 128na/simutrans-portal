<?php

namespace App\Console\Commands\TweetLog;

use App\Services\TwitterAnalytics\AggregateTweetLogService;
use App\Services\TwitterAnalytics\ImportTweetService;
use App\Services\TwitterAnalytics\ResolveArticleService;
use App\Services\TwitterAnalytics\SearchTweetService;
use Illuminate\Console\Command;

class Import extends Command
{
    protected $signature = 'tweet_log:import {path : path to json file.}';

    protected $description = 'Import tweet logs from json file.';

    public function __construct(
        private ImportTweetService $importTweetService,
        private SearchTweetService $searchTweetService,
        private ResolveArticleService $resolveArticleService,
        private AggregateTweetLogService $aggregateTweetLogService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $path = base_path($this->argument('path'));
        $result = $this->importTweetService->importJson($path);
        $resolved = $this->resolveArticleService->titleToArticles($result);
        $articleIds = $this->aggregateTweetLogService->firstOrCreateTweetLogs($resolved);
        $this->aggregateTweetLogService->updateOrCreateTweetLogSummary($articleIds);
    }
}
