<?php

namespace App\Services\TimeBucket;
 
use Illuminate\Support\Carbon;

class TimeBucketCalculatorService {

    const DEFAULT_BUCKET_PERIOD_IN_HOURS = 4;

    public static function getTimeBucketPeriodInHours(): int {
        $bucketPeriodInHours = env('TIME_BUCKET_PERIOD');
        if (empty($bucketPeriodInHours)) {
            $bucketPeriodInHours = self::DEFAULT_BUCKET_PERIOD_IN_HOURS;
        }

        return $bucketPeriodInHours;
    }

    public function calculateTimeBucketTimestamp($timestamp = null): int {
        $bucketPeriodInHours = self::getTimeBucketPeriodInHours();

        if (empty($timestamp)) {
            $timestamp = Carbon::now()->timestamp;
        }
        
        return $timestamp - ($timestamp % ($bucketPeriodInHours * 60 * 60));
    }
}  