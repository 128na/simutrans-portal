<?php
namespace App\Services;

use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Models\Article;
use App\Models\User;
use Carbon\CarbonImmutable as Carbon;

class ArticleAnalyticsService extends Service
{
    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function getArticles(User $user, SearchRequest $request)
    {
        $start_date = new Carbon($request->start_date);
        $end_date = new Carbon($request->end_date);
        $type = $request->type;
        $ids = $request->ids;

        $period_query = $this->getPeriodQuery($type, $start_date, $end_date);

        return $user->articles()
            ->select('id')
            ->whereIn('id', $ids)
            ->with([
                'viewCounts' => $period_query,
                'conversionCounts' => $period_query,
            ])->get();
    }

    private function getPeriodQuery($type, $start_date, $end_date)
    {
        $period = $this->getPeriod($type, $start_date, $end_date);
        $type_id = $this->getTypeId($type);

        return function ($query) use ($type_id, $period) {
            $query->select('article_id', 'count', 'period')
                ->where('type', $type_id)->whereBetween('period', $period);
        };
    }

    private function getPeriod($type, $start_date, $end_date)
    {
        switch ($type) {
            case 'daily':
                return [$start_date->format('Ymd'), $end_date->format('Ymd')];
            case 'monthly':
                return [$start_date->format('Ym'), $end_date->format('Ym')];
            case 'yearly':
                return [$start_date->format('Y'), $end_date->format('Y')];
        }
    }
    private function getTypeId($type)
    {
        switch ($type) {
            case 'daily':
                return 1;
            case 'monthly':
                return 2;
            case 'yearly':
                return 3;
        }
    }

}
