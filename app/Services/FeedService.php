<?php
namespace App\Services;

use App\Models\Article;

class FeedService extends Service
{
    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function getAllFeedItems($type = null)
    {
        return $this->model
            ->active()
            ->addon()
            ->limit($this->per_page)
            ->get();
    }
}
