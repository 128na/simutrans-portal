<?php

namespace Tests\Unit\Services\GoogleAnalytics;

use App\Services\GoogleAnalytics\ConvertReportResponseService;
use Google\Service\AnalyticsReporting\DateRangeValues;
use Google\Service\AnalyticsReporting\GetReportsResponse;
use Google\Service\AnalyticsReporting\Report;
use Google\Service\AnalyticsReporting\ReportData;
use Google\Service\AnalyticsReporting\ReportRow;
use Mockery\MockInterface as M;
use Tests\UnitTestCase;

class ConvertReportResponseServiceTest extends UnitTestCase
{
    private function getSUT(): ConvertReportResponseService
    {
        return app(ConvertReportResponseService::class);
    }

    public function test()
    {
        /** @var GetReportsResponse */
        $mock = $this->mock(GetReportsResponse::class, function (M $m) {
            $m->shouldReceive('getReports')->andReturn([$this->mock(Report::class, function (M $m) {
                $m->shouldReceive('getData')->andReturn($this->mock(ReportData::class, function (M $m) {
                    $m->shouldReceive('getRows')->andReturn([$this->mock(ReportRow::class, function (M $m) {
                        $m->shouldReceive('getDimensions')->andReturn(['/articles/dummy']);
                        $m->shouldReceive('getMetrics')->andReturn([$this->mock(DateRangeValues::class, function (M $m) {
                            $m->shouldReceive('getValues')->andReturn([334]);
                        })]);
                    })]);
                }));
            })]);
        });

        $service = $this->getSUT();

        $result = $service->rankingResponseToArray($mock);

        $this->assertEquals('/articles/dummy', array_keys($result)[0], 'キーがpagepath');
        $this->assertEquals(334, array_values($result)[0], '値がpageviews');
    }
}
