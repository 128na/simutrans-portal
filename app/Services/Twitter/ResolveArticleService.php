<?php

namespace App\Services\Twitter;

use App\Models\Article;
use App\Models\Article\TweetLog;
use App\Repositories\Article\TweetLogRepository;
use App\Repositories\ArticleRepository;

class ResolveArticleService
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private TweetLogRepository $tweetLogRepository,
    ) {
    }

    /**
     * @return TweetData[]
     */
    public function resolveByTweetDatas(array $tweetDataArray): array
    {
        $tweetIds = array_map(fn (TweetData $tweetData) => $tweetData->id, $tweetDataArray);
        $storedTweetLogs = $this->tweetLogRepository->findByIds($tweetIds);

        $titles = array_map(fn (TweetData $tweetData) => $tweetData->title, $tweetDataArray);
        $articles = $this->articleRepository->findByTitles($titles);

        return array_map(function (TweetData $tweetData) use ($storedTweetLogs, $articles) {
            if ($id = $storedTweetLogs->first(fn (TweetLog $t) => $tweetData->id === $t->id)?->article_id) {
                $tweetData->articleId = $id;
            } elseif ($id = $articles->first(fn (Article $a) => $tweetData->title === $a->title)?->id) {
                $tweetData->articleId = $id;
            }

            return $tweetData;
        }, $tweetDataArray);
    }
}
