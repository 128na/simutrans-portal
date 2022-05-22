<?php

namespace App\Services\GoogleAnalytics;

use Google\Service\AnalyticsReporting\DateRange;
use Google\Service\AnalyticsReporting\Dimension;
use Google\Service\AnalyticsReporting\DimensionFilter;
use Google\Service\AnalyticsReporting\DimensionFilterClause;
use Google\Service\AnalyticsReporting\GetReportsRequest;
use Google\Service\AnalyticsReporting\GetReportsResponse;
use Google\Service\AnalyticsReporting\Metric;
use Google\Service\AnalyticsReporting\OrderBy;
use Google\Service\AnalyticsReporting\ReportRequest;
use Google\Service\AnalyticsReporting\Resource\Reports;

class ReportingService
{
    public function __construct(private Reports $reports)
    {
    }

    public function getPageViewRanking(string $viewId, string $startDate = '7daysAgo', string $endDate = 'today', int $limit = 50): GetReportsResponse
    {
        $request = app(ReportRequest::class);
        $request->setViewId($viewId);
        $request->setDateRanges([$this->getDateRange($startDate, $endDate)]);
        $request->setDimensions([$this->getDimensionPagePath()]);
        $request->setDimensionFilterClauses([$this->getDimensionFilterArticle()]);
        $request->setMetrics([$this->getMetricPageViews(), $this->getMetricSessions()]);
        $request->setOrderBys([$this->getOrderByPageViews(), $this->getOrderBySessions()]);
        $request->setPageSize($limit);

        $body = app(GetReportsRequest::class);
        $body->setReportRequests([$request]);

        return $this->reports->batchGet($body);
    }

    private function getDateRange($startDate, string $endDate): DateRange
    {
        $r = app(DateRange::class);
        $r->setStartDate($startDate);
        $r->setEndDate($endDate);

        return $r;
    }

    private function getDimensionPagePath(): Dimension
    {
        $d = app(Dimension::class);
        $d->setName('ga:pagePath');

        return $d;
    }

    private function getMetricSessions(): Metric
    {
        $m = app(Metric::class);
        $m->setExpression('ga:sessions');
        $m->setAlias('sessions');

        return $m;
    }

    private function getOrderBySessions(): OrderBy
    {
        $o = app(OrderBy::class);
        $o->setFieldName('ga:sessions');
        $o->setOrderType('VALUE');
        $o->setSortOrder('DESCENDING');

        return $o;
    }

    private function getMetricPageViews(): Metric
    {
        $m = app(Metric::class);
        $m->setExpression('ga:pageviews');
        $m->setAlias('pageviews');

        return $m;
    }

    private function getOrderByPageViews(): OrderBy
    {
        $o = app(OrderBy::class);
        $o->setFieldName('ga:pageviews');
        $o->setOrderType('VALUE');
        $o->setSortOrder('DESCENDING');

        return $o;
    }

    private function getDimensionFilterArticle(): DimensionFilterClause
    {
        $dimensionFilter = app(DimensionFilter::class);
        $dimensionFilter->setDimensionName('ga:pagePath');
        $dimensionFilter->setOperator('REGEXP');
        $dimensionFilter->setExpressions(['\A\/articles\/.*\z']);
        $dimensionFilterClause = app(DimensionFilterClause::class);
        $dimensionFilterClause->setFilters([$dimensionFilter]);

        return $dimensionFilterClause;
    }
}
