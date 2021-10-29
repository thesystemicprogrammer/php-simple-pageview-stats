<?php

namespace Integration;

use TestCase;
use Dotenv\Dotenv;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthFailureTest extends TestCase {

    public function setup(): void {
        Dotenv::createUnsafeMutable(dirname(__DIR__), 'env/.env-authorization.test')->load();
        parent::setup();
    }

    public function testUnauthorizedPostNoHeader(): void {
        $this->post('/api/pageview');
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);

    }
    public function testUnauthorizedGetNoHeader(): void {
        $this->get('/api/pageview');
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUnauthorizedGetPeriodNoHeader(): void {
        $this->get('/api/pageview/period');
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUnauthorizedPostWithHeader(): void {
        $this->json('POST', '/api/pageview', [], ['Authorization' => 'nonsense']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);

    }
    public function testUnauthorizedGetWithHeader(): void {
        $this->json('GET', '/api/pageview', [], ['Authorization' => 'nonsense']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUnauthorizedGetPeriodWithHeader(): void {
        $this->json('GET', '/api/pageview/period', [], ['Authorization' => 'nonsense']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

   
}
