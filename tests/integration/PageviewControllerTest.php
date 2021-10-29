<?php

namespace Integration;

use TestCase;
use Dotenv\Dotenv;
use App\Models\Pageview;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class PageviewControllerTest extends TestCase {
    use DatabaseMigrations;

    public function setup(): void {
        parent::setup();
        Dotenv::createUnsafeMutable(dirname(__DIR__), 'env/.env-authorization.test')->load();
        $this->disableAbortLocalhostMiddleware();
    }

    public function testPostPageview(): void {
        Dotenv::createUnsafeMutable(dirname(__DIR__), 'env/.env-authorization.test')->load();
        $this->json('POST', '/api/pageview', ['uri' => '/newpage'], ['Authorization' => 'post_test']);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }

    public function testPostPageviewAlreadyExists(): void {
        $this->json('POST', '/api/pageview', ['uri' => '/newpage'], ['Authorization' => 'post_test']);
        $this->json('POST', '/api/pageview', ['uri' => '/newpage'], ['Authorization' => 'post_test']);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    public function testGetPageview(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview', [], ['Authorization' => 'get_test'])
            ->seeJson(['views' => '10']);
    }

    public function testGetPageviewPeriodAll(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview/period', [], ['Authorization' => 'get_test']);
        $this->assertEquals('[{"timestamp":"1635091200","uri":"\/my-page","views":"5"},{"timestamp":"1635105600","uri":"\/my-page","views":"5"}]', $this->response->getContent());
    }

    public function testGetPageviewPeriodFrom(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview/period?from=1635091201', [], ['Authorization' => 'get_test']);
        $this->assertEquals('[{"timestamp":"1635105600","uri":"\/my-page","views":"5"}]', $this->response->getContent());
    }

    public function testGetPageviewPeriodTo(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview/period?to=1635105599', [], ['Authorization' => 'get_test']);
        $this->assertEquals('[{"timestamp":"1635091200","uri":"\/my-page","views":"5"}]', $this->response->getContent());
    }

    public function testGetPageviewPeriodFromEdge(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview/period?from=1635091200', [], ['Authorization' => 'get_test']);
        $this->assertEquals('[{"timestamp":"1635091200","uri":"\/my-page","views":"5"},{"timestamp":"1635105600","uri":"\/my-page","views":"5"}]', $this->response->getContent());
    }

    public function testGetPageviewPeriodToEdge(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview/period?to=1635105600', [], ['Authorization' => 'get_test']);
        $this->assertEquals('[{"timestamp":"1635091200","uri":"\/my-page","views":"5"},{"timestamp":"1635105600","uri":"\/my-page","views":"5"}]', $this->response->getContent());
    }

    public function testGetPageviewPeriodFromToEmpty(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview/period?from=1635105601&to=1635105655', [], ['Authorization' => 'get_test']);
        $this->assertEquals('[]', $this->response->getContent());
    }

    public function testGetPageviewPeriodFromGreaterThanTo(): void {
        $this->createPageviewData();
        $this->json('GET', '/api/pageview/period?from=1635105601&to=1635105600', [], ['Authorization' => 'get_test'])
            ->seeStatusCode(Response::HTTP_BAD_REQUEST);
    }

    private function disableAbortLocalhostMiddleware(): void {
        $this->app->instance(\App\Http\Middleware\AbortLocalhost::class, new class {
            public function handle($request, $next) {
                return $next($request);
            }
        });
    }

    private function createPageviewData(): void {
        Carbon::setTestNow(Carbon::parse('2021-10-24 16:31:32'));
        Pageview::factory()->create();
        Carbon::setTestNow(Carbon::parse('2021-10-24 22:15:20'));
        Pageview::factory()->create();
    }
}
