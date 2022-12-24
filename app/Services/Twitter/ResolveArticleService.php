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
     * @param array<TweetData> $tweetDataArray
     * @return array<TweetData>
     */
    public function resolveByTweetDatas(array $tweetDataArray): array
    {
        $tweetDataArray = $this->resolveById($tweetDataArray);
        $tweetDataArray = $this->resolveByTitle($tweetDataArray);

        return $tweetDataArray;
    }

    /**
     * @param array<TweetData> $tweetDataArray
     * @return array<TweetData>
     */
    private function resolveById(array $tweetDataArray): array
    {
        $tweetIds = array_map(fn (TweetData $tweetData) => $tweetData->id, $tweetDataArray);
        $tweetLogs = $this->tweetLogRepository->findByIds($tweetIds);

        return array_map(function (TweetData $tweetData) use ($tweetLogs) {
            if ($id = $tweetLogs->first(fn (TweetLog $t) => $tweetData->id === $t->id)?->article_id) {
                $tweetData->articleId = $id;
            }

            return $tweetData;
        }, $tweetDataArray);
    }

    /**
     * @param array<TweetData> $tweetDataArray
     * @return array<TweetData>
     */
    private function resolveByTitle(array $tweetDataArray): array
    {
        $titles = array_map(
            fn (TweetData $tweetData) => $tweetData->title,
            array_filter($tweetDataArray, fn (TweetData $tweetData) => is_null($tweetData->articleId))
        );
        $articles = $this->articleRepository->findByTitles($titles);

        return array_map(function (TweetData $tweetData) use ($articles) {
            if ($id = $articles->first(fn (Article $a) => $tweetData->title === $a->title)?->id) {
                $tweetData->articleId = $id;
            }

            return $tweetData;
        }, $tweetDataArray);
    }
}
