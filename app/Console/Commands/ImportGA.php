<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ConversionCount;
use Carbon\CarbonPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ga';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import conversion count from Google Analytics';

    private $article_set;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $analytics = $this->initializeAnalytics();
        $profile = $this->getFirstProfileId($analytics);

        // 取得できた最古のデータから今日まで
        $period = new CarbonPeriod('2016-05-31', '1 days', today()->format('Y-m-d'));
        /**
         * immutable版を指定
         * @see https://github.com/briannesbitt/Carbon/blob/master/src/Carbon/CarbonPeriod.php#L530
         */
        $period->setDateClass(CarbonImmutable::class);

        // 記事をタイトルでインデックス
        $this->article_set = collect([]);
        Article::all()->map(function($article) {
            $this->article_set->put($article->title, $article);
        });

        DB::beginTransaction();
        foreach ($period as $date) {
            $records = $this->getRecords($analytics, $profile, $date);

            if(is_null($records)) {
                $this->warn('nodata: '.$date->format('Y-m-d'));
                continue;
            }
            foreach ($records as $record) {
                $this->applyRecord($record, $date);
            }
        }
        DB::commit();
    }
    private function applyRecord($record, $date)
    {
        $title = $record[0];
        $count = $record[1];

        $article = $this->article_set->get($title);

        if(is_null($article)) {
            return $this->warn('missing article: '.$title);
        }
        for ($i=0; $i < $count; $i++) {
            ConversionCount::countUp($article, $date);
        }
        return $this->info("add : {$title}, {$count}");
    }

    /**
     * @see https://developers.google.com/analytics/devguides/reporting/core/v3/quickstart/service-php#helloanalyticsphp
     */
    private function initializeAnalytics()
    {
        // Creates and returns the Analytics Reporting service object.

        // Use the developers console and download your service account
        // credentials in JSON format. Place them in this directory or
        // change the key file location if necessary.
        $KEY_FILE_LOCATION = resource_path('apikey/ga.json');

        // Create and configure a new client object.
        $client = new \Google_Client();
        $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new \Google_Service_Analytics($client);

        return $analytics;
    }

    private function getFirstProfileId($analytics)
    {
        // Get the user's first view (profile) ID.

        // Get the list of accounts for the authorized user.
        $accounts = $analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            $firstAccountId = $items[0]->getId();

            // Get the list of properties for the authorized user.
            $properties = $analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

            if (count($properties->getItems()) > 0) {
            $items = $properties->getItems();
            $firstPropertyId = $items[0]->getId();

            // Get the list of views (profiles) for the authorized user.
            $profiles = $analytics->management_profiles
                ->listManagementProfiles($firstAccountId, $firstPropertyId);

            if (count($profiles->getItems()) > 0) {
                $items = $profiles->getItems();

                // Return the first view (profile) ID.
                return $items[0]->getId();

            } else {
                throw new \Exception('No views (profiles) found for this user.');
            }
            } else {
            throw new \Exception('No properties found for this user.');
            }
        } else {
            throw new \Exception('No accounts found for this user.');
        }
    }

    private function getRecords($analytics, $profileId, $date)
    {
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.
        $results = $analytics->data_ga->get(
            "ga:{$profileId}",
            $date->format('Y-m-d'),
            $date->format('Y-m-d'),
            'ga:totalEvents',
            ['dimensions' => 'ga:EventLabel']);
        return $results->getRows();
    }

}
