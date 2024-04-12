<?php

declare(strict_types=1);

namespace App\Actions\Ranking;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final readonly class Update
{
    private const int SIZE = 100;

    public function __construct(
        private ArticleRepository $articleRepository,
        private CarbonImmutable $now,
    ) {
    }

    public function __invoke(): void
    {
        DB::statement('delete from rankings');
        $this->articleRepository->chunkAggregatedRanking($this->now, self::SIZE, function (Collection $articles, int $round): void {
            $ranks = $articles
                ->map(fn (Article $article, int $index): array => [
                    'article_id' => $article->id,
                    'rank' => 1 + $index + self::SIZE * ($round - 1),
                ])
                ->all();

            DB::table('rankings')->insertOrIgnore($ranks);
        });

        JobUpdateRelated::dispatchSync();
    }
}
