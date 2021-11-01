<?php

namespace App\Services\Consolidator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TimeBucket\TimeBucketCalculatorService;

class PageviewRefererConsolidationService {

    private TimeBucketCalculatorService $calculatorService;
    
    public function __construct(TimeBucketCalculatorService $calculatorService) {
        $this->calculatorService = $calculatorService;
    }

    public function __invoke(TimeBucketCalculatorService $calculatorService): void {
        $this->consolidatePageviews($calculatorService);
    }

    public function consolidatePageviews(): void {
        DB::transaction(function () {
            $timestamp = $this->calculatorService->calculateTimeBucketTimestamp();

            $subquery = DB::table('referer_pageviews')->selectRaw('timestamp, uri, count(*) AS views')->where('timestamp', '<', $timestamp)->groupBy(['timestamp', 'uri']);
            $consolidatedRecords = DB::table('pageviews')->insertUsing(['timestamp', 'uri', 'views'], $subquery);
            $deletedRecords = DB::table('referer_pageviews')->where('timestamp', '<', $timestamp)->delete();
            
            if ($consolidatedRecords > 0 || $deletedRecords > 0) {
                Log::notice('Pageview database consolidated', ['consolidated' => $consolidatedRecords, 'deleted' => $deletedRecords, 'timeBucket' => date('c', $timestamp)]);
            }
        });
    }
}
