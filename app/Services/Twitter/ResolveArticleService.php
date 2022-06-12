<?php

namespace App\Services\Twitter;

use App\Models\Article;
use App\Repositories\ArticleRepository;

class ResolveArticleService
{
    public function __construct(private ArticleRepository $articleRepository)
    {
    }

    /**
     * @return TweetData[]
     */
    public function titleToArticles(array $tweetDataArray): array
    {
        $titles = array_map(fn (TweetData $tweetData) => $tweetData->title, $tweetDataArray);

        $articles = $this->articleRepository->findByTitles($titles);

        return array_map(function (TweetData $tweetData) use ($articles) {
            $tweetData->articleId = $articles->first(fn (Article $a) => $tweetData->title === $a->title)?->id;

            return $tweetData;
        }, $tweetDataArray);
    }
}
