<?php

namespace App\Services\TimeBucket;
 
use Illuminate\Support\Carbon;

class TimeBucketCalculatorService {

    const DEFAULT_BUCKET_PERIOD_IN_HOURS = 4;

    public static function getTimeBucketPeriodInHours(): int {
        return env('TIME_BUCKET_PERIOD', self::DEFAULT_BUCKET_PERIOD_IN_HOURS);
    }

    public function calculateTimeBucketTimestamp($timestamp = null): int {
        $bucketPeriodInHours = $this->getTimeBucketPeriodInHours();

        if (empty($timestamp)) {
            $timestamp = Carbon::now()->timestamp;
        }
        
        return $timestamp - ($timestamp % ($bucketPeriodInHours * 60 * 60));
    }
}  