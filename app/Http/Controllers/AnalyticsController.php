<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\Dimension;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsDataClient;

    public function __construct()
    {
        $this->analyticsDataClient = new BetaAnalyticsDataClient();
    }

    public function fetchData(Request $request)
    {
        $propertyId = '460219113';
        $period = $request->input('period', 7);
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        $dateRange = new DateRange([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);

        // Request 1: Aggregate data for top metrics
        $metrics = [
            new Metric(['name' => 'screenPageViews']),
            new Metric(['name' => 'sessions']),
            new Metric(['name' => 'totalUsers']),
            new Metric(['name' => 'bounceRate']),
            new Metric(['name' => 'userEngagementDuration']),
            new Metric(['name' => 'engagementRate']),
        ];

        $aggregateResponse = $this->analyticsDataClient->runReport([
            'property' => 'properties/' . $propertyId,
            'dateRanges' => [$dateRange],
            'metrics' => $metrics,
        ]);

        // Calculate average engagement time for top metrics
        $totalEngagementTime = $aggregateResponse->getRows()[0]->getMetricValues()[4]->getValue(); // in seconds
        $totalUsers = $aggregateResponse->getRows()[0]->getMetricValues()[2]->getValue();
        $averageEngagementTimePerUser = $totalUsers > 0 ? ($totalEngagementTime / $totalUsers) : 0;
        $avgMinutes = floor($averageEngagementTimePerUser / 60);
        $avgSeconds = $averageEngagementTimePerUser % 60;

        $engagementRate = $aggregateResponse->getRows()[0]->getMetricValues()[5]->getValue();

        $metricsData = [
            'views' => $aggregateResponse->getRows()[0]->getMetricValues()[0]->getValue(),
            'visits' => $aggregateResponse->getRows()[0]->getMetricValues()[1]->getValue(),
            'visitors' => $totalUsers,
            'bounceRate' => number_format($aggregateResponse->getRows()[0]->getMetricValues()[3]->getValue() * 100, 2) . '%',
            'avgVisitTime' => $avgMinutes . ' M ' . $avgSeconds . ' S',
            'engagementRate' => number_format($engagementRate * 100, 2) . '%',
        ];

        // Request 2: Daily breakdown for views and visitors (for the chart)
        $dimensions = [
            new Dimension(['name' => 'date']),
        ];

        $dailyResponse = $this->analyticsDataClient->runReport([
            'property' => 'properties/' . $propertyId,
            'dateRanges' => [$dateRange],
            'metrics' => 
            [
                new Metric(['name' => 'screenPageViews']), 
                new Metric(['name' => 'totalUsers']), 
                new Metric(['name' => 'engagementRate']),
            ],
            'dimensions' => $dimensions,
        ]);

        $dailyData = [];

        // Generate all dates within the range with default values
        $dateRangeArray = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateRangeArray[$date->format('Ymd')] = [
                'date' => $date->format('Y-m-d'),
                'views' => 0,
                'visitors' => 0,
                'engagementRate' => 0,
            ];
        }

        // Fill in the actual data from the Google Analytics response
        foreach ($dailyResponse->getRows() as $row) {
            $dateKey = $row->getDimensionValues()[0]->getValue();
            $dateRangeArray[$dateKey] = [
                'date' => Carbon::createFromFormat('Ymd', $dateKey)->toDateString(),
                'views' => $row->getMetricValues()[0]->getValue(),
                'visitors' => $row->getMetricValues()[1]->getValue(),
                'engagementRate' => number_format($row->getMetricValues()[2]->getValue() * 100, 2),
            ];
        }

        // Convert to an array to pass to the frontend
        $dailyData = array_values($dateRangeArray);

        // Include both total metrics and daily data in the response
        return response()->json([
            'metricsData' => $metricsData,
            'dailyData' => $dailyData,
        ]);
    }
}
