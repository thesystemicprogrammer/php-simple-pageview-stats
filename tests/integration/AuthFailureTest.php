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

    public function testUnauthorizedGetNoHeader(): void {
        $this->get('/api/pageview');
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUnauthorizedGetPeriodNoHeader(): void {
        $this->get('/api/pageview/period');
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUnauthorizedGetPostNoHeader(): void {
        $this->post('/api/pageview');
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }


    public function testUnauthorizedGetWithHeader(): void {
        $this->json('GET', '/api/pageview', [], ['X-API-KEY' => 'nonsense']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUnauthorizedGetPeriodWithHeader(): void {
        $this->json('GET', '/api/pageview/period', [], ['X-API-KEY' => 'nonsense']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUnauthorizedGetPostWithHeader(): void {
        $this->json('POST', '/api/pageview', [], ['X-API-KEY' => 'nonsense']);
        $this->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
    }

    
}
