<?php

namespace App\Services\GoogleAnalytics;

use Google\Service\AnalyticsReporting\GetReportsResponse;

class ConvertReportResponseService
{
    public function rankingResponseToArray(GetReportsResponse $response): array
    {
        $result = [];
        foreach ($response->getReports() as $report) {
            foreach ($report->getData()->getRows() as $row) {
                $dimensions = $row->getDimensions();
                $pagePath = $dimensions[0];
                $metrics = $row->getMetrics();
                $count = $metrics[0]->getValues()[0];
                $result[$pagePath] = $count;
            }
        }

        return $result;
    }
}
