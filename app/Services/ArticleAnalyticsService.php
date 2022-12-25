<?php

namespace App\Services;

use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable as Carbon;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use UnexpectedValueException;

class ArticleAnalyticsService extends Service
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @return Collection<int, Article>
     */
    public function findArticles(User $user, SearchRequest $request): Collection
    {
        $startDate = new Carbon($request->start_date);
        $endDate = new Carbon($request->end_date);
        $type = $request->type;
        $ids = $request->ids;

        $periodQuery = $this->getPeriodQuery($type, $startDate, $endDate);

        return $this->articleRepository->findAllForAnalytics($user, $ids, $periodQuery);
    }

    private function getPeriodQuery(string $type, Carbon $startDate, Carbon $endDate): Closure
    {
        $period = $this->getPeriod($type, $startDate, $endDate);
        $type_id = $this->getTypeId($type);

        return function ($query) use ($type_id, $period) {
            $query->select('article_id', 'count', 'period')
                ->where('type', $type_id)->whereBetween('period', $period);
        };
    }

    /**
     * @return array<string>
     */
    private function getPeriod(string $type, Carbon $startDate, Carbon $endDate): array
    {
        switch ($type) {
            case 'daily':
                return [$startDate->format('Ymd'), $endDate->format('Ymd')];
            case 'monthly':
                return [$startDate->format('Ym'), $endDate->format('Ym')];
            case 'yearly':
                return [$startDate->format('Y'), $endDate->format('Y')];
        }
        throw new UnexpectedValueException(sprintf('unknown type provided: %s', $type));
    }

    private function getTypeId(string $type): int
    {
        switch ($type) {
            case 'daily':
                return 1;
            case 'monthly':
                return 2;
            case 'yearly':
                return 3;
        }
        throw new UnexpectedValueException(sprintf('unknown type provided: %s', $type));
    }
}
