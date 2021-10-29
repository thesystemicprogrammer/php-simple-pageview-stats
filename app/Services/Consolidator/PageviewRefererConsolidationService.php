<?php

namespace App\Services\Consolidator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\DatabaseConsolidationException;
use App\Services\TimeBucket\TimeBucketCalculatorService;

class PageviewRefererConsolidationService {

    public function __invoke(TimeBucketCalculatorService $calculatorService): void {
        $this->consolidatePageviews($calculatorService);
    }

    public function consolidatePageviews(TimeBucketCalculatorService $calculatorService): void {
        DB::transaction(function () use ($calculatorService) {
            $timestamp = $calculatorService->calculateTimeBucketTimestamp();
            
            $subquery = DB::table('referer_pageviews')->selectRaw('timestamp, uri, count(*) AS views')->where('timestamp', '<', $timestamp)->groupBy(['timestamp', 'uri']);
            $consolidatedRecords = DB::table('pageviews')->insertUsing(['timestamp', 'uri', 'views'], $subquery);
            $deletedRecords = DB::table('referer_pageviews')->where('timestamp', '<', $timestamp)->delete();

            Log::notice('Pageview database consolidated', ['consolidated' => $consolidatedRecords, 'deleted' => $deletedRecords, 'timeBucket' => date('c', $timestamp)]);
        });
    }
}
