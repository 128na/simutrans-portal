<?php

namespace App\Console\Commands\TweetLog;

use App\Services\Twitter\AggregateTweetLogService;
use App\Services\Twitter\ResolveArticleService;
use App\Services\Twitter\SearchTweetService;
use Illuminate\Console\Command;

class UpdateByList extends Command
{
    protected $signature = 'tweet_log:update_by_list';

    protected $description = 'Update tweet logs';

    public function __construct(
        private SearchTweetService $searchTweetService,
        private ResolveArticleService $resolveArticleService,
        private AggregateTweetLogService $aggregateTweetLogService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $collection = $this->searchTweetService->searchTweetsByList(config('twitter.list_id'));

            foreach ($collection->chunk(100) as $chunk) {
                $result = $chunk->toArray();
                $resolved = $this->resolveArticleService->titleToArticles($result);
                $articleIds = $this->aggregateTweetLogService->updateOrCreateTweetLogs($resolved);
                $this->aggregateTweetLogService->updateOrCreateTweetLogSummary($articleIds);
            }
        } catch (\Throwable $e) {
            report($e);

            return 1;
        }

        return 0;
    }
}
