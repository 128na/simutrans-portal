<?php

namespace App\Repositories\Article;

use App\Models\Article\TweetLogSummary;
use App\Repositories\BaseRepository;

class TweetLogSummaryRepository extends BaseRepository
{
    /**
     * @var TweetLogSummary
     */
    protected $model;

    public function __construct(TweetLogSummary $model)
    {
        $this->model = $model;
    }
}
