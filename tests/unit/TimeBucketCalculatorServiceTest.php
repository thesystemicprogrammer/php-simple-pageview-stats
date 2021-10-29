<?php
namespace Unit;

use TestCase;
use Dotenv\Dotenv;
use Illuminate\Support\Carbon;
use App\Services\TimeBucket\TimeBucketCalculatorService;

class TimeBucketCalculatorServiceTest extends TestCase
{
    public function testCalculationWithPassedDate(): void {

        $calculator = new TimeBucketCalculatorService();
        $timestamp = $calculator->calculateTimeBucketTimestamp(Carbon::parse('2021-10-24 19:31:32')->timestamp);

        $this->assertEquals($timestamp, Carbon::parse('2021-10-24 16:00:00')->timestamp);
    }

    public function testCalculationWithNoPassedDate(): void {
        
        Carbon::setTestNow(Carbon::parse('2021-10-24 16:31:32'));
        $calculator = new TimeBucketCalculatorService();

        $timestamp = $calculator->calculateTimeBucketTimestamp();

        $this->assertEquals($timestamp, Carbon::parse('2021-10-24 16:00:00')->timestamp);

        Carbon::setTestNow();
    }

    public function testCalculationWithSetTimeBucketPeriod(): void {
        Dotenv::createUnsafeMutable(dirname(__DIR__), 'env/.env-timebucket.test')->load();
        Carbon::setTestNow(Carbon::parse('2021-10-24 19:31:32'));
        $calculator = new TimeBucketCalculatorService();
        
        $timestamp = $calculator->calculateTimeBucketTimestamp();
        $this->assertEquals($timestamp, Carbon::parse('2021-10-24 18:00:00')->timestamp);

        Carbon::setTestNow();
    }
}
