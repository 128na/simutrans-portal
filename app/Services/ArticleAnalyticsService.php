<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ArticleAnalyticsType;
use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable as Carbon;
use Illuminate\Database\Eloquent\Collection;
use UnexpectedValueException;

class ArticleAnalyticsService
{
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
    }

    /**
     * @return Collection<int, Article>
     */
    public function findArticles(User $user, SearchRequest $searchRequest): Collection
    {
        $startDate = new Carbon((string) $searchRequest->string('start_date'));
        $endDate = new Carbon((string) $searchRequest->string('end_date'));
        $type = (string) $searchRequest->string('type');
        /** @var int[] */
        $ids = $searchRequest->input('ids');
        $typeId = $this->getTypeId($type);
        $period = $this->getPeriod($type, $startDate, $endDate);

        return $this->articleRepository->findAllForAnalytics($user, $ids, $typeId, $period);
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

    private function getTypeId(string $type): ArticleAnalyticsType
    {
        return match ($type) {
            'daily' => ArticleAnalyticsType::Daily,
            'monthly' => ArticleAnalyticsType::Monthly,
            'yearly' => ArticleAnalyticsType::Yearly,
            default => throw new UnexpectedValueException(sprintf('unknown type provided: %s', $type)),
        };
    }
}
