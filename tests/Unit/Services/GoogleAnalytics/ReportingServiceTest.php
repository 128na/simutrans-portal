<?php

namespace Tests\Unit\Services\GoogleAnalytics;

use App\Services\GoogleAnalytics\ReportingService;
use Google\Service\AnalyticsReporting\GetReportsRequest;
use Google\Service\AnalyticsReporting\GetReportsResponse;
use Google\Service\AnalyticsReporting\ReportRequest;
use Google\Service\AnalyticsReporting\Resource\Reports;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class ReportingServiceTest extends UnitTestCase
{
    private function getSUT(): ReportingService
    {
        return app(ReportingService::class);
    }

    public function test()
    {
        $this->mock(ReportRequest::class, function (MockInterface $m) {
            $m->shouldReceive('setViewId');
            $m->shouldReceive('setDateRanges');
            $m->shouldReceive('setDimensions');
            $m->shouldReceive('setDimensionFilterClauses');
            $m->shouldReceive('setMetrics');
            $m->shouldReceive('setOrderBys');
            $m->shouldReceive('setPageSize');
        });

        $this->mock(GetReportsRequest::class, function (MockInterface $m) {
            $m->shouldReceive('setReportRequests');
        });

        $this->mock(Reports::class, function (MockInterface $m) {
            $m->shouldReceive('batchGet')->andReturn(new GetReportsResponse());
        });

        $service = $this->getSUT();

        $service->getPageViewRanking('dummy');
    }
}
