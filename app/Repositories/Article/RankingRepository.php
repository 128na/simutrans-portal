<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Models\Article\Ranking;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

/**
 * @extends BaseRepository<Ranking>
 */
class RankingRepository extends BaseRepository
{
    public function __construct(Ranking $ranking)
    {
        parent::__construct($ranking);
    }

    /**
     * @param  LazyCollection<int,\App\Models\Article>  $lazyCollection
     */
    public function recreate(LazyCollection $lazyCollection): void
    {
        DB::transaction(function () use ($lazyCollection): void {
            $this->model->query()->delete();
            $rank = 1;
            foreach ($lazyCollection as $article) {
                $this->store([
                    'rank' => $rank,
                    'article_id' => $article->id,
                ]);
                $rank++;
            }
        });
    }
}
