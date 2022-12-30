<?php

declare(strict_types=1);

namespace App\Console\Commands\TweetLog;

use App\Services\Twitter\AggregateTweetLogService;
use App\Services\Twitter\ImportTweetService;
use App\Services\Twitter\ResolveArticleService;
use Exception;
use Illuminate\Console\Command;

class Import extends Command
{
    protected $signature = 'tweet_log:import {path : path to json file.}';

    protected $description = 'Import tweet logs from json file.';

    public function __construct(
        private ImportTweetService $importTweetService,
        private ResolveArticleService $resolveArticleService,
        private AggregateTweetLogService $aggregateTweetLogService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $path = $this->getPath();
            $result = $this->importTweetService->importJson($path);
            $resolved = $this->resolveArticleService->resolveByTweetDatas($result);
            $articleIds = $this->aggregateTweetLogService->firstOrCreateTweetLogs($resolved);
            $this->aggregateTweetLogService->updateOrCreateTweetLogSummary($articleIds);
        } catch (\Throwable $e) {
            report($e);

            return 1;
        }

        return 0;
    }

    private function getPath(): string
    {
        $path = $this->argument('path');
        if (! is_string($path)) {
            throw new Exception('path is not string');
        }

        return base_path($path);
    }
}
