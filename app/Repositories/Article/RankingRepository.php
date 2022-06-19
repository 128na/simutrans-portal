<?php

namespace App\Repositories\Article;

use App\Models\Article\Ranking;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class RankingRepository extends BaseRepository
{
    /**
     * @var Ranking
     */
    protected $model;

    public function __construct(Ranking $model)
    {
        $this->model = $model;
    }

    public function recreate(LazyCollection $articles): void
    {
        DB::transaction(function () use ($articles) {
            $this->model->query()->delete();
            $rank = 1;
            foreach ($articles as $article) {
                $this->store([
                    'rank' => $rank,
                    'article_id' => $article->id,
                ]);
                ++$rank;
            }
        });
    }
}
