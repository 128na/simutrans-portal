<?php

namespace App\Services;

use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable as Carbon;
use Closure;
use UnexpectedValueException;

class ArticleAnalyticsService extends Service
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getArticles(User $user, SearchRequest $request)
    {
        $start_date = new Carbon($request->start_date);
        $end_date = new Carbon($request->end_date);
        $type = $request->type;
        $ids = $request->ids;

        $periodQuery = $this->getPeriodQuery($type, $start_date, $end_date);

        return $this->articleRepository->findAllForAnalytics($user, $ids, $periodQuery);
    }

    private function getPeriodQuery(string $type, Carbon $start_date, Carbon $end_date): Closure
    {
        $period = $this->getPeriod($type, $start_date, $end_date);
        $type_id = $this->getTypeId($type);

        return function ($query) use ($type_id, $period) {
            $query->select('article_id', 'count', 'period')
                ->where('type', $type_id)->whereBetween('period', $period);
        };
    }

    private function getPeriod(string $type, Carbon $start_date, Carbon $end_date): array
    {
        switch ($type) {
            case 'daily':
                return [$start_date->format('Ymd'), $end_date->format('Ymd')];
            case 'monthly':
                return [$start_date->format('Ym'), $end_date->format('Ym')];
            case 'yearly':
                return [$start_date->format('Y'), $end_date->format('Y')];
        }
        throw new UnexpectedValueException(sprintf('unknown type provided: %s', $type));
    }

    private function getTypeId($type): int
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
