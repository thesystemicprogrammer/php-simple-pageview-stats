<?php

namespace Unit;

use TestCase;
use App\Models\Pageview;
use App\Models\RefererPageview;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Services\Consolidator\PageviewRefererConsolidationService;
use App\Services\TimeBucket\TimeBucketCalculatorService;

class PageviewRefererConsolidationServiceTest extends TestCase {
    use DatabaseMigrations;

    private $calculator;

    public function setup(): void {
        $this->calculator = new TimeBucketCalculatorService();
        parent::setup();
    }

    public function testEmptyDatabasesToConsolidate(): void {
        $consolidator = new PageviewRefererConsolidationService($this->calculator);

        $consolidator->consolidatePageviews();

        $this->assertEmpty(RefererPageview::all());
    }

    public function testConsolidatePreviousBucket(): void {
        Carbon::setTestNow(Carbon::parse('2021-10-24 16:31:32'));
        RefererPageview::factory()->count(5)->create();
        RefererPageview::factory()->count(5)->setAlternativeUri()->create();
        Carbon::setTestNow(Carbon::parse('2021-10-24 22:15:20'));
        $consolidator = new PageviewRefererConsolidationService($this->calculator);

        $consolidator->consolidatePageviews();

        $this->assertEmpty(RefererPageview::all());
        $this->assertEquals(2, Pageview::all()->count());

        Carbon::setTestNow();
    }

    public function testReferPageviewsFromCurrentBucketRemain(): void {
        Carbon::setTestNow(Carbon::parse('2021-10-24 16:31:32'));
        RefererPageview::factory()->count(5)->create();
        RefererPageview::factory()->count(5)->setAlternativeUri()->create();
        Carbon::setTestNow(Carbon::parse('2021-10-24 22:15:20'));
        RefererPageview::factory()->count(5)->create();
        RefererPageview::factory()->count(5)->setAlternativeUri()->create();
        $consolidator = new PageviewRefererConsolidationService($this->calculator);

        $consolidator->consolidatePageviews();

        $this->assertEquals(10, RefererPageview::all()->count());
        $this->assertEquals(2, Pageview::all()->count());

        Carbon::setTestNow();
    }
}
