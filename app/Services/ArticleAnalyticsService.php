<?php

declare(strict_types=1);

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
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
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

        return static function ($query) use ($type_id, $period): void {
            $query->select('article_id', 'count', 'period')
                ->where('type', $type_id)->whereBetween('period', $period);
        };
    }

    /**
     * @return array<string>
     */
    private function getPeriod(string $type, Carbon $startDate, Carbon $endDate): array
    {
        return match ($type) {
            'daily' => [$startDate->format('Ymd'), $endDate->format('Ymd')],
            'monthly' => [$startDate->format('Ym'), $endDate->format('Ym')],
            'yearly' => [$startDate->format('Y'), $endDate->format('Y')],
            default => throw new UnexpectedValueException(sprintf('unknown type provided: %s', $type)),
        };
    }

    private function getTypeId(string $type): int
    {
        return match ($type) {
            'daily' => 1,
            'monthly' => 2,
            'yearly' => 3,
            default => throw new UnexpectedValueException(sprintf('unknown type provided: %s', $type)),
        };
    }
}
